-- STEP 0
-- https://github.com/pgvector/pgvector

CREATE EXTENSION vector;

-- STEP 1

create table embeddings (
  id bigserial primary key,
  content text,
  embedding vector(1536)
);

-- STEP 2

create or replace function match_embeddings (
  query_embedding vector(1536),
  match_threshold float,
  match_count int
)
returns table (
  id bigint,
  content text,
  similarity float
)
language sql stable
as $$
  select
    embeddings.id,
    embeddings.content,
    1 - (embeddings.embedding <=> query_embedding) as similarity
  from embeddings
  where 1 - (embeddings.embedding <=> query_embedding) > match_threshold
  order by similarity desc
  limit match_count;
$$;

-- STEP 4

create index on embeddings using ivfflat (embedding vector_cosine_ops)
with
  (lists = 100);