<?php

// O código viola vários princípios SOLID

class PetshopController {
    // SRP (Princípio da Responsabilidade Única) violado aqui:
    // Esta classe está fazendo várias coisas: validação, persistência de dados e controle de operações. 
    // Isso fere o SRP porque ela tem múltiplas responsabilidades.
    
    public function cadastrarCliente($nome, $email, $telefone) {
        // SRP: A validação dos dados deveria estar em uma classe separada. 
        // Atualmente, essa responsabilidade está misturada aqui no controlador.
        if (empty($nome) || empty($email)) {
            throw new Exception("Dados inválidos");
        }

        // SRP + DIP: Simulação de salvamento no banco de dados está embutida aqui. 
        // O PetshopController depende diretamente da implementação do "banco de dados". 
        // Isso viola o DIP (Princípio da Inversão de Dependência) porque ele deveria depender de abstrações, 
        // e o salvamento no banco deveria estar em uma camada separada.
        echo "Cliente cadastrado: $nome, $email, $telefone\n";
    }

    // OCP (Princípio Aberto/Fechado) violado aqui:
    // Se precisarmos de um novo método de busca ou novas regras de busca, teremos que modificar este código.
    public function buscarCliente($nome) {
        // Simulação simples de busca
        if ($nome == "João") {
            return "Cliente encontrado: João, joao@email.com, (11) 99999-9999";
        } else {
            return "Cliente não encontrado";
        }
    }

    // LSP (Princípio da Substituição de Liskov) violado potencialmente:
    // Se houvesse uma classe que herdasse de PetshopController, ela poderia alterar essa lógica, 
    // mas talvez o comportamento esperado não seja mantido em todas as subclasses.
    public function atualizarCliente($nome, $novoEmail, $novoTelefone) {
        echo "Dados atualizados: $nome, $novoEmail, $novoTelefone\n";
    }

    // ISP (Princípio da Segregação de Interfaces) não respeitado:
    // Se houvesse outras operações no futuro, esta classe pode acabar implementando métodos que não são necessários para todos os casos.
    public function excluirCliente($nome) {
        echo "Cliente $nome excluído\n";
    }
}

// Testando código sem SOLID
$petshop = new PetshopController();
$petshop->cadastrarCliente("João", "joao@email.com", "(11) 99999-9999");
echo $petshop->buscarCliente("João");
$petshop->atualizarCliente("João", "novoemail@email.com", "(11) 88888-8888");
$petshop->excluirCliente("João");

?>
