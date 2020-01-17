<?php

namespace Reweb\Job\Backend;

/**
 * Classe do Caixa Eletrônico
 *
 * @author Huberto Gastal Mayer <hubertogm@gmail.com>
 */
class Caixa
{
  private $contas = [
    1234 => [
      "saldo_cc" => 600,
      "saldo_cp" => 1000
    ],
    4321 => [
      "saldo_cc" => 600,
      "saldo_cp" => 1000
    ]
  ];

  private $conta = null;

  private $conta_transferencia = null;

  private $tipo_conta = 'cc';

  private $taxa_saque = [
    'cc' => 2.5,
    'cp' => 0.8
  ];

  // function __construct() {
  //   setlocale(LC_MONETARY, 'it_IT');
  // }

    /**
     * Caixa Eletrônico
     *
     * @return boolean
     */
    public function caixa()
    {
        system('clear');
        echo "Bem-vindo ao CyberBank - Caixa Eletrônico\n";
        if($this->autenticate()) {
          $this->minha_conta();
        } else {
          $this->caixa();
        }
        return true;
    }

    /**
     * Autenticação da Conta
     *
     * @return boolean
     */
    private function autenticate() {
      echo "Digite o número de sua conta para continuar:\nObs: Para fim de testes, digite o número '1234'\n";
      $handle = fopen ("php://stdin","r");
      $line = fgets($handle);
      if(array_key_exists((int)$line, $this->contas)){
        $this->conta = (int)$line;
        return true;
      } else {
        echo "Conta inexistente!\n";
        sleep(2);
        return false;
      }
      fclose($handle);
    }


    /**
     * Apresentação de saldos e opções
     *
     * @return boolean
     */
    private function minha_conta() {
      system('clear');
      echo "Bem-vindo à sua conta!\n";
      echo "Saldo atual em conta corrente: B$ " . $this->money($this->contas[$this->conta]['saldo_cc']) . "\n";
      echo "Saldo atual em conta poupança: B$ " . $this->money($this->contas[$this->conta]['saldo_cp']) . "\n";
      $this->options();
      return true;
    }

    /**
     * Apresentação de opções
     *
     * @return boolean
     */
    public function options() {
      echo "Digite o número da opção que você deseja realizar:\n\n";
      echo "1 - Depósito\n";
      echo "2 - Saque\n";
      echo "3 - Transferência\n";

      $handle = fopen ("php://stdin","r");
      $line = fgets($handle);
      switch ((int)$line) {
        case 1:
          $this->deposito();
          break;

        case 2:
          $this->saque();
          break;

        case 3:
          $this->transferencia();
          break;

        default:
          $this->minha_conta();
          break;
      }
      fclose($handle);
    }

    /**
     * Realização de DEPÓSITO
     *
     * @return boolean
     */
    public function deposito() {
      echo "REALIZAR UM DEPÓSITO:\n\n";
      $this->escolha_tipo_conta();

      echo "Digite o valor a ser depositado (use vírgula para centavos): ";
      $handle = fopen ("php://stdin","r");
      $line = fgets($handle);
      $value = (float)str_replace(",", ".", $line);
      $this->contas[$this->conta]['saldo_' . $this->tipo_conta] = $this->contas[$this->conta]['saldo_' . $this->tipo_conta] + $value;

      $this->minha_conta();
      fclose($handle);
    }

    /**
     * Realização de SAQUE
     *
     * @return boolean
     */
    public function saque() {
      echo "REALIZAR UM SAQUE:\n\n";
      $this->escolha_tipo_conta();

      echo "Digite o valor a ser sacado (use vírgula para centavos): ";
      $handle = fopen ("php://stdin","r");
      $line = fgets($handle);
      $value = (float)str_replace(",", ".", $line);
      $temp = $this->contas[$this->conta]['saldo_' . $this->tipo_conta] - $value - $this->taxa_saque[$this->tipo_conta];
      if($temp < 0) {
        echo "Não há saldo em conta para esse valor de saque!\n";
        sleep(2);
        $this->minha_conta();
      } else {
        $this->contas[$this->conta]['saldo_' . $this->tipo_conta] = $temp;
      }

      $this->minha_conta();
      fclose($handle);
    }

    /**
     * Realização de TRANSFERÊNCIA
     *
     * @return boolean
     */
    public function transferencia() {
      echo "REALIZAR UMA TRANSFERÊNCIA:\n\n";
      $this->escolha_tipo_conta();

      echo "Digite a conta para a qual você deseja transferir:\nObs: Para fim de testes, digite o número '4321'\n";
      $handle = fopen ("php://stdin","r");
      $line = fgets($handle);
      if(array_key_exists((int)$line, $this->contas)){
        $this->conta_transferencia = (int)$line;
      } else {
        echo "Conta inexistente!\n";
        sleep(2);
        $this->minha_conta();
        return false;
      }
      fclose($handle);

      echo "Digite o valor a ser transferido (use vírgula para centavos): ";
      $handle = fopen ("php://stdin","r");
      $line = fgets($handle);
      $value = (float)str_replace(",", ".", $line);
      $temp = $this->contas[$this->conta]['saldo_' . $this->tipo_conta] - $value;
      if($temp < 0) {
        echo "Não há saldo em conta para esse valor de transferência!\n";
        sleep(2);
        $this->minha_conta();
      } else {
        $this->contas[$this->conta]['saldo_' . $this->tipo_conta] = $temp;
        $this->contas[$this->conta_transferencia]['saldo_cc'] = $this->contas[$this->conta_transferencia]['saldo_cc'] + $value;
        echo "Transferência realizada com sucesso para a Conta Corrente " . $this->conta_transferencia . "!\n";
        sleep(3);
      }

      $this->minha_conta();
      fclose($handle);
      return true;
    }

    /**
     * Escolha de tipo de conta
     *
     * @return boolean
     */
    public function escolha_tipo_conta($question = "Escolha o tipo de conta:\n") {
      echo $question;
      echo "1 - Conta Corrente\n";
      echo "2 - Conta Poupança\n";
      $handle = fopen ("php://stdin","r");
      $line = fgets($handle);
      switch ((int)$line) {
        case 1:
          $this->tipo_conta = 'cc';
          break;

        case 2:
          $this->tipo_conta = 'cp';
          break;

        default:
          $this->tipo_conta = 'cc';
          break;
      }
      fclose($handle);
      return true;
    }

    /**
     * Formata número float em Monetário tipo biteris
     *
     * @return boolean
     */
    private function money($value) {
      return number_format($value, 2, ',', '.');
    }

}
