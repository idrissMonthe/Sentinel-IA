<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CreateAdmin extends Command
{
    protected $signature = 'sentinel:admin-create {email : Adresse qui recevra le code de connexion} {--nom=Administrateur} {--prenom=Sentinel}';

    protected $description = 'Créer un administrateur et afficher son mot de passe généré une seule fois';

    public function handle(): int
    {
        $data = [
            'email' => strtolower(trim((string) $this->argument('email'))),
            'nom' => $this->option('nom'),
            'prenom' => $this->option('prenom'),
        ];
        $validator = Validator::make($data, [
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            return self::FAILURE;
        }

        $password = Str::password(20, symbols: false);
        User::create($data + [
            'password' => $password,
            'role' => UserRole::ADMINISTRATEUR,
            'statut' => 'actif',
            'tentatives_echouees' => 0,
        ]);
        $this->info('Administrateur créé : '.$data['email']);
        $this->line('Mot de passe : '.$password);
        $this->comment('Conservez ce mot de passe. La connexion demande également le code envoyé à cette adresse email.');

        return self::SUCCESS;
    }
}
