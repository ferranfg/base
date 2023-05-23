<?php

namespace Ferranfg\Base\Models;

use OpenAI;
use Pgvector\Laravel\Vector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Assistance extends Model
{
    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'pgsql';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'embedding' => Vector::class
    ];

    /**
     * Get the OpenAI client
     *
     * @return \OpenAI
     */
    public static function getOpenAI()
    {
        return OpenAI::client(
            config('services.openai.secret'),
            config('services.openai.key')
        );
    }

    /**
     * Create a new Embedding instance from an input string
     *
     * @param  string  $input
     * @return \Ferranfg\Base\Models\Embedding
     */
    public static function embeddingFromInput($input)
    {
        // OpenAI recommends replacing newlines with spaces for best results
        $input = str_replace("\n", " ", $input);
        $input = str_replace("\r", " ", $input);
        $input = preg_replace('!\s+!', ' ', $input);

        $response = self::getOpenAI()->embeddings()->create([
            'model' => 'text-embedding-ada-002',
            'input' => trim($input)
        ]);

        $assistance = new self;
        $assistance->content = $input;
        $assistance->embedding = $response->embeddings[0]->embedding;

        return $assistance;
    }

    /**
     * Match embeddings against the database
     *
     * @param  \Pgvector\Laravel\Vector  $embeddings
     * @param  float  $match_threshold
     * @param  int  $match_count
     * @return array
     */
    public static function match(Vector $embeddings, $match_threshold, $match_count)
    {
        return DB::connection((new self)->getConnectionName())
            ->select('SELECT * from match_assistances(?, ?, ?)', [
                $embeddings,
                $match_threshold,
                $match_count
            ]);
    }

    /**
     * Get the assistance that matches the given query
     *
     * @param  string  $query
     * 
     */
    public static function completion($query, $match_threshold = 0.78, $match_count = 10)
    {
        $assistance = self::embeddingFromInput($query);
        $assistances = self::match($assistance->embedding, $match_threshold, $match_count);

        [$class, $method] = Str::parseCallback(config('base.assistance_system'));

        $prompt = implode("\n", app($class)->$method());
        $prompt = "{$prompt}\n\nContext sections:\n\n";

        foreach ($assistances as $assistance)
        {
            $prompt = "{$prompt}{$assistance->content}\n";
        }

        $prompt = "{$prompt}Query:\"\"\"\n{$query}\n\"\"\"\n\nResponse:";

        return self::getOpenAI()->completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => $prompt,
            'max_tokens' => 512,
            'temperature' => 0,
        ]);
    }
}