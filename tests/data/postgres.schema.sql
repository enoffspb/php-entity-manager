DROP TABLE IF EXISTS "php_em_example";
DROP SEQUENCE IF EXISTS "php_em_example_pk_sequence";

CREATE SEQUENCE "php_em_example_pk_sequence" INCREMENT 1 MINVALUE 1 START 1;

CREATE TABLE "php_em_example" (
  "id" INT NULL DEFAULT nextval('php_em_example_pk_sequence') PRIMARY KEY,
  "name" VARCHAR(256) NOT NULL,
  "custom" VARCHAR(256) NULL DEFAULT NULL
);
