CREATE TABLE public.meta_tags (
  id SERIAL,
  url VARCHAR,
  title TEXT,
  description TEXT,
  keywords TEXT,
  CONSTRAINT meta_tags_pkey PRIMARY KEY(id),
  CONSTRAINT meta_tags_url_key UNIQUE(url)
) 
WITH (oids = false);
