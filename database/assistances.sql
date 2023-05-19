-- STEP 0
-- https://github.com/pgvector/pgvector

CREATE EXTENSION vector;

-- STEP 1

create table assistances (
  id bigserial primary key,
  content text,
  embedding vector(1536),
  created_at timestamp default now(),
  updated_at timestamp default now()
);

-- STEP 2

create or replace function match_assistances (
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
    assistances.id,
    assistances.content,
    1 - (assistances.embedding <=> query_embedding) as similarity
  from assistances
  where 1 - (assistances.embedding <=> query_embedding) > match_threshold
  order by similarity desc
  limit match_count;
$$;

-- STEP 4

create index on assistances using ivfflat (embedding vector_cosine_ops)
with
  (lists = 100);