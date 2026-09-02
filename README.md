# CSS Hot Reload (Dev Only)

Módulo para desenvolvimento local em Drupal que atualiza o CSS na página sem dar reload, assim que o ficheiro muda no disco — como as extensões de live-reload para HTML puro.

**Não usar em produção.**

## Instalação

```bash
ddev composer require paulosouzx/css-hot-reload:dev-main
ddev drush en css_hot_reload -y
```

## Como usar

1. Vai a **Configuration > Development > CSS Hot Reload** (`/admin/config/development/css-hot-reload`)
2. Ativa a checkbox e ajusta o intervalo de polling se quiseres (default: 1000ms)
3. Desativa a agregação de CSS localmente (Performance settings), senão o browser só vê um `<link>` agregado

## Como funciona

- O estado (ativo/inativo) é guardado via **State API**, não Config, por isso não há schema, e a definição nunca entra em config sync
- Um JS faz polling a um endpoint interno que devolve o `filemtime()` de cada CSS carregado na página
- Quando um ficheiro muda, o `<link>` correspondente é substituído (sem reload da página)

## Desativar

Mesmo sítio: `/admin/config/development/css-hot-reload` → desmarca a checkbox.