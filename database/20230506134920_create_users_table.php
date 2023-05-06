<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class CreateUsersTable extends AbstractMigration
{
    protected function up(): void
    {
        $this->execute('
            CREATE TABLE public.users (
                                  id serial4 NOT NULL,
                                  first_name varchar(255) NULL,
                                  last_name varchar(255) NULL,
                                  pesel varchar(11) NULL,
                                  email varchar(255) NULL,
                                  contact_emails jsonb NULL,
                                  CONSTRAINT users_email_key UNIQUE (email),
                                  CONSTRAINT users_pesel_key UNIQUE (pesel),
                                  CONSTRAINT users_pkey PRIMARY KEY (id)
            );
        ');
        $this->execute('CREATE INDEX idx_users_login_email ON public.users USING btree (email);');
        $this->execute('CREATE INDEX idx_users_pesel ON public.users USING btree (pesel);');
    }

    protected function down(): void
    {
        $this->execute('DROP TABLE public.users;');
    }
}
