CREATE SCHEMA news;


CREATE TABLE news.items (
  id SERIAL, 
  description TEXT, 
  text TEXT, 
  create_date TIMESTAMP WITHOUT TIME ZONE DEFAULT now() NOT NULL, 
  author INTEGER, 
  image VARCHAR(100), 
  user_id INTEGER, 
  owner VARCHAR(10), 
  video VARCHAR(100), 
  audio VARCHAR(100), 
  header TEXT, 
  "position" SMALLINT, 
  CONSTRAINT items_pkey PRIMARY KEY(id)
) WITHOUT OIDS;


CREATE TABLE news.comments (
  id SERIAL,
  reply_id INTEGER,
  news_id INTEGER NOT NULL,
  user_id INTEGER NOT NULL,
  datetime TIMESTAMP WITHOUT TIME ZONE DEFAULT now() NOT NULL,
  comment TEXT NOT NULL,
  CONSTRAINT comments_pkey PRIMARY KEY(id),
  CONSTRAINT comments_event_comment_fk FOREIGN KEY (reply_id)
    REFERENCES news.comments(id)
    ON DELETE NO ACTION
    ON UPDATE CASCADE
    NOT DEFERRABLE,
  CONSTRAINT comments_event_fk FOREIGN KEY (news_id)
    REFERENCES news.items(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
    NOT DEFERRABLE
)
WITH (oids = false);

CREATE INDEX comments_event_comment_ix ON news.comments
  USING btree (reply_id);

CREATE INDEX comments_event_ix ON news.comments
  USING btree (news_id);

CREATE INDEX comments_user_ix ON news.comments
  USING btree (user_id);




CREATE TABLE news.gallery (
  id SERIAL, 
  news_id INTEGER NOT NULL, 
  file VARCHAR NOT NULL, 
  uploaded TIMESTAMP WITHOUT TIME ZONE DEFAULT now() NOT NULL, 
  PRIMARY KEY(id)
) WITHOUT OIDS;