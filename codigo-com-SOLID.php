<?php

// SRP (Princípio da Responsabilidade Única) RESPEITADO: 
// Extraímos a responsabilidade de validação para uma classe separada, 
// assim o controlador (PetshopController) não precisa lidar com isso.
class ValidadorCliente {
    public function validarDados($nome, $email) {
        if (empty($nome) || empty($email)) {
            throw new Exception("Dados inválidos");
        }
    }
}

// DIP (Princípio da Inversão de Dependência) RESPEITADO: 
// Agora o PetshopController não depende diretamente de uma implementação concreta de persistência de dados, 
// ele depende de uma abstração (interface ClienteRepositoryInterface). Isso também facilita mudanças futuras, 
// pois podemos criar diferentes implementações (como salvar em um banco de dados real).
interface ClienteRepositoryInterface {
    public function salvar($cliente);
    public function buscar($nome);
    public function atualizar($cliente);
    public function excluir($nome);
}

// OCP (Princípio Aberto/Fechado) RESPEITADO: 
// Agora, a classe ClienteRepository está aberta para extensão (podemos criar novos tipos de repositórios), 
// mas fechada para modificação. Se precisarmos de uma nova forma de salvar ou buscar, podemos criar uma nova implementação 
// sem modificar a classe existente.
class ClienteRepository implements ClienteRepositoryInterface {
    private $clientes = [];

    public function salvar($cliente) {
        $this->clientes[$cliente['nome']] = $cliente;
        echo "Cliente cadastrado com sucesso: " . $cliente['nome'] . "\n";
    }

    public function buscar($nome) {
        if (isset($this->clientes[$nome])) {
            return $this->clientes[$nome];
        } else {
            return "Cliente não encontrado\n";
        }
    }

    public function atualizar($cliente) {
        if (isset($this->clientes[$cliente['nome']])) {
            $this->clientes[$cliente['nome']] = $cliente;
            echo "Dados atualizados para " . $cliente['nome'] . "\n";
        } else {
            echo "Cliente não encontrado\n";
        }
    }

    public function excluir($nome) {
        if (isset($this->clientes[$nome])) {
            unset($this->clientes[$nome]);
            echo "Cliente $nome excluído\n";
        } else {
            echo "Cliente não encontrado\n";
        }
    }
}

// SRP e DIP RESPEITADOS: 
// O controlador agora não precisa mais lidar com validação e persistência de dados diretamente. 
// Ele recebe serviços de validação e repositório via injeção de dependências, e delega essas responsabilidades às classes corretas. 
// Isso também melhora a modularidade e facilita a manutenção.
class PetshopController {
    private $validador;
    private $repositorio;

    // DIP: Injeção de dependências no construtor. Isso permite que a implementação específica seja passada na construção da classe,
    // e facilita a substituição das dependências (como criar um repositório diferente no futuro).
    public function __construct(ValidadorCliente $validador, ClienteRepositoryInterface $repositorio) {
        $this->validador = $validador;
        $this->repositorio = $repositorio;
    }

    // SRP RESPEITADO: O controlador agora não faz validação ou persistência de dados diretamente. Ele apenas orquestra o fluxo, 
    // delegando as responsabilidades corretas para as classes de Validador e Repositório.
    public function cadastrarCliente($nome, $email, $telefone) {
        $this->validador->validarDados($nome, $email);
        $cliente = ['nome' => $nome, 'email' => $email, 'telefone' => $telefone];
        $this->repositorio->salvar($cliente);
    }

    // OCP e LSP RESPEITADOS: As operações CRUD foram movidas para a interface e o repositório concreto. 
    // O controlador não precisa se preocupar com mudanças na lógica de busca. Isso mantém o código fechado para modificações 
    // e aberto para extensões (podemos criar novas formas de buscar clientes).
    public function buscarCliente($nome) {
        return $this->repositorio->buscar($nome);
    }

    // LSP RESPEITADO: A lógica de atualização foi encapsulada em uma interface e implementada no repositório. 
    // Se fosse necessário criar subclasses de repositórios, o comportamento esperado seria mantido em todas as subclasses.
    public function atualizarCliente($nome, $novoEmail, $novoTelefone) {
        $cliente = ['nome' => $nome, 'email' => $novoEmail, 'telefone' => $novoTelefone];
        $this->repositorio->atualizar($cliente);
    }

    // ISP RESPEITADO: A interface ClienteRepositoryInterface está segregada em operações essenciais (CRUD), 
    // então qualquer nova implementação pode escolher implementar apenas os métodos que realmente precisa, sem sobrecarga.
    public function excluirCliente($nome) {
        $this->repositorio->excluir($nome);
    }
}

// Testando o código refatorado com SOLID
$validador = new ValidadorCliente();
$repositorio = new ClienteRepository();
$petshop = new PetshopController($validador, $repositorio);

$petshop->cadastrarCliente("João", "joao@email.com", "(11) 99999-9999");
echo $petshop->buscarCliente("João");
$petshop->atualizarCliente("João", "novoemail@email.com", "(11) 88888-8888");
$petshop->excluirCliente("João");

?>
