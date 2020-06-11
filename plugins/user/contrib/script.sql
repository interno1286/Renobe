
CREATE TABLE "public"."users" (
  "id" SERIAL, 
  "email" VARCHAR NOT NULL, 
  "password" VARCHAR(64) NOT NULL, 
  "fio" VARCHAR(100), 
  "user_type" VARCHAR DEFAULT 'user' NOT NULL, 
  "blocked" BOOLEAN DEFAULT false NOT NULL,
  PRIMARY KEY("id")
) WITHOUT OIDS;

CREATE TABLE "public"."change_pass_tokens" (
  "id" SERIAL, 
  "datetime" TIMESTAMP WITHOUT TIME ZONE DEFAULT now() NOT NULL, 
  "user_id" INTEGER NOT NULL, 
  "token" VARCHAR(50) NOT NULL, 
  CONSTRAINT "change_pass_tokens_pkey" PRIMARY KEY("id")
) WITHOUT OIDS;

CREATE RULE "change_pass_tokens_clean_trash_rl" AS ON INSERT TO "public"."change_pass_tokens" 
DO (
DELETE FROM change_pass_tokens
  WHERE change_pass_tokens.datetime < (now() - interval '5 days');
);


CREATE TABLE public.auth_log (
  id SERIAL,
  type VARCHAR(50),
  message TEXT,
  data TEXT,
  user_id INTEGER,
  datetime TIMESTAMP WITHOUT TIME ZONE DEFAULT now() NOT NULL,
  PRIMARY KEY(id)
) ;


ALTER TABLE public.auth_log
  ADD CONSTRAINT auth_log_fk FOREIGN KEY (user_id)
    REFERENCES public.users(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
    NOT DEFERRABLE;