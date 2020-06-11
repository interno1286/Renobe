CREATE TABLE public.pages (
  id SERIAL,
  name VARCHAR(200),
  description TEXT,
  keywords TEXT,
  path VARCHAR(200),
  skin VARCHAR(100),
  content TEXT,
  md5_content VARCHAR(50),
  language VARCHAR(10) DEFAULT 'ru'::character varying NOT NULL,
  CONSTRAINT pages_pkey PRIMARY KEY(id)
) 
WITH (oids = false);

CREATE UNIQUE INDEX pages_path_lng_uniq ON public.pages
  USING btree (path, language);

CREATE TABLE public.pages_backup (
  id SERIAL,
  page_id INTEGER,
  backup_date TIMESTAMP WITHOUT TIME ZONE DEFAULT now() NOT NULL,
  content TEXT,
  CONSTRAINT pages_backup_pkey PRIMARY KEY(id),
  CONSTRAINT pages_backup_fk FOREIGN KEY (page_id)
    REFERENCES public.pages(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
    NOT DEFERRABLE
) 
WITH (oids = false);

CREATE RULE pages_rl AS ON UPDATE TO public.pages 
DO (
INSERT INTO pages_backup (page_id, content) 
  VALUES (new.id, new.content);
);
