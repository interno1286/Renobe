CREATE SCHEMA comments;

CREATE TABLE comments.items (
  id SERIAL,
  "for" TEXT,
  author TEXT,
  "from" TEXT,
  user_id INTEGER,
  ip INET,
  date TIMESTAMP WITHOUT TIME ZONE DEFAULT now() NOT NULL,
  text TEXT,
  in_ans_for INTEGER,
  PRIMARY KEY(id)
);


ALTER TABLE comments.items
  ADD CONSTRAINT items_ans_fk FOREIGN KEY (in_ans_for)
    REFERENCES comments.items(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
    NOT DEFERRABLE;


CREATE INDEX items_for_what_idx ON comments.items
  USING btree (for_what);