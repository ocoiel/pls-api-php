<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;
use Illuminate\Http\Request;
use Eduardokum\LaravelBoleto\Pessoa as Pessoa;
use Eduardokum\LaravelBoleto\Boleto\Banco\Bancoob as Bancoob;
use Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab240\Banco\Bancoob as BancoobRemesa;

class BoletoController extends Controller
{
    public function __construct(Database $database)
    {
        $this->users = $database->getReference('users');
    }

    public function index()
    {
        $beneficiario = new Pessoa([
            'documento' => '00.000.000/0000-00',
            'nome'      => 'CYB Soluções',
            'cep'       => '00000-000',
            'endereco'  => 'Praça Tiradentes, 2802',
            'bairro'    => 'Centro',
            'uf'        => 'Rio de Janeiro',
            'cidade'    => 'Rio de Janeiro',
        ]);

        $value = $this->users->getValue();
        // dd($value);

        $pagador = new Pessoa([
                'nome'      => 'Cliente',
                'endereco'  => 'Rua um, 123',
                'bairro'    => 'Bairro',
                'cep'       => '99999-999',
                'uf'        => 'UF',
                'cidade'    => 'CIDADE',
                'documento' => '168.337.807-57',
            ]
        );

        $dataVencimento = new Carbon();
        $bancoob = new Bancoob([
            'logo' => __DIR__ . DIRECTORY_SEPARATOR . 'arquivos' . DIRECTORY_SEPARATOR . 'logo1.png',
            'dataVencimento'         => $dataVencimento,
            'dataVencimento'         => new \Carbon\Carbon(),
            'valor'                  => 100,
            'multa'                  => false,
            'juros'                  => false,
            'numero'                 => 1,
            'numeroDocumento'        => 1,
            'pagador'                => $pagador,
            'beneficiario'           => $beneficiario,
            'carteira'               => 1,
            'agencia'                => 1111,
            'convenio'               => 123123,
            'conta'                  => 22222,
            'descricaoDemonstrativo' => [''],
            'instrucoes'             => ['instrucao 1', 'instrucao 2', 'instrucao 3'],
            'aceite'                 => 'S',
            'especieDoc'             => 'DM',
        ]);

        $remessa = new BancoobRemesa(
            [
                'agencia'       => 1111,
                'conta'         => 22222,
                'carteira'      => 1,
                'convenio'      => 123123,
                'idremessa'    => 1,
                'beneficiario'  => $beneficiario,
            ]
        );

        $pdf = new \Eduardokum\LaravelBoleto\Boleto\Render\Pdf();
        $pdf->addBoleto($bancoob);
        $pdf->gerarBoleto($pdf::OUTPUT_STANDARD, __DIR__ . DIRECTORY_SEPARATOR . 'arquivos' . DIRECTORY_SEPARATOR . 'bancoob1.pdf');


        $remessa->addBoleto($bancoob);
        echo $remessa->save(__DIR__ . DIRECTORY_SEPARATOR . 'arquivos' . DIRECTORY_SEPARATOR . 'bancoob.txt');
    }
}
