### Requirements
- PHP = 8.1
- Laravel >= 9.14
- SQLite database

### Instalação

- Execute o comando `composer install`

- Para iniciar o servidor da API, execute o comando: `php artisan serve --port=3000`

- Para executar os testes, execute o comando: `php artisan test`

### Boas práticas

Este projeto implementa várias boas práticas de desenvolvimento de software:

#### 1. Arquitetura em Camadas (Clean Architecture)
- **Controllers**: Gerenciamento de requisições HTTP
- **Services**: Regras de negócio
- **Repositories**: Acesso a dados
- **Resources**: Transformação de dados
- **Models**: Entidades de domínio

#### 2. Princípios SOLID
- **Single Responsibility**: Cada classe tem uma única responsabilidade
- **Dependency Inversion**: Uso de injeção de dependência
- **Interface Segregation**: Interfaces específicas para cada contexto
- **Open/Closed**: Extensão através de classes e não modificação

#### 3. Design Patterns
- Repository Pattern
- Service Pattern
- Factory Pattern
- Resource Pattern para transformação de dados
- Provider Pattern para registro de serviços

#### 4. Boas Práticas Laravel
- Form Requests para validação
- Resources para transformação de dados
- Middleware para autenticação
- Migrations para versionamento de banco
- Enums para tipos constantes
- Service Container para injeção de dependência

#### 5. Clean Code
- Nomes descritivos para métodos e variáveis
- Funções pequenas e focadas
- Tratamento adequado de exceções
- Documentação clara e objetiva
- Tipagem forte com PHP 8.1

#### 6. Segurança
- Validação de tokens API
- Proteção contra CSRF
- Sanitização de inputs
- Rate Limiting configurado
- Encriptação de cookies

#### 7. Testes
- Testes unitários
- Testes de integração
- Testes de API
- Factories para dados de teste
- Ambiente de teste com SQLite

#### 8. Gerenciamento de Dados
- Transações para consistência
- Relacionamentos Eloquent
- Migrations versionadas
- Validações robustas
- Tratamento de exceções específicas

#### 9. API RESTful
- Endpoints padronizados
- Respostas HTTP apropriadas
- Recursos bem definidos
- Validações consistentes
- Documentação clara

