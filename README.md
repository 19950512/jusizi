# J U S I Z I

## Descrição

O Jusizi é um projeto focado atualmente em estudos de arquitetura, design e testes.
####  Como o negócio é voltado 100% ao Brasil, o projeto é desenvolvido em português.

## Arquitetura

A arquitetura do projeto é baseada em sistemas distribuídos, com comunicação assíncrona e orientada a eventos. A comunicação entre os serviços é feita através de mensageria, utilizando o RabbitMQ como broker.
O projeto é composto por 3 camadas principais: `Aplicacao`, `Dominio` e `Infraestrutura`.

## Design

O design do projeto é baseado em DDD (Domain Driven Design), com a separação de responsabilidades entre as camadas de aplicação, domínio e infraestrutura.

## Testes

O projeto está sendo desenvolvido utilizando o Pest como framework de testes.
A Cobertura de testes pode ser visualizada [aqui - Coverage](https://19950512.github.io/jusizi).


## Tecnologias

- PHP 8.3
- RabbitMQ
- Docker Compose
- ~~Postgresql~~ (Em breve)
- ~~Redis~~ (Em breve)
- ~~Nginx~~ (Em breve)
- ~~Flutter~~ (Em breve)
