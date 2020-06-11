CREATE TABLE public.simple_text_data (
  id SERIAL,
  code VARCHAR(30) NOT NULL,
  content TEXT,
  draft TEXT,
  CONSTRAINT simple_text_data_pkey PRIMARY KEY(id)
) 
WITH (oids = false);

CREATE UNIQUE INDEX simple_text_data_idx ON public.simple_text_data
  USING btree (code);

CREATE TABLE public.simple_image_data (
  src_hash VARCHAR NOT NULL,
  link TEXT,
  custom_full_version TEXT,
  CONSTRAINT simple_image_data_pkey PRIMARY KEY(src_hash)
) 
WITH (oids = false);