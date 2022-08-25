
![Adeliom](https://adeliom.com/public/uploads/2017/09/Adeliom_logo.png)
[![Quality gate](https://sonarcloud.io/api/project_badges/quality_gate?project=agence-adeliom_easy-editor-bundle)](https://sonarcloud.io/dashboard?id=agence-adeliom_easy-editor-bundle)

# Easy Editor Bundle

Provide a flexible content editor for Easyadmin.


## Features

- Ability to create custom blocks
- Twig extension to render the content


## Installation

Install with composer

```bash
composer require agence-adeliom/easy-editor-bundle
```

## Documentation

### Usage

#### Entity

```php
class Article
{
    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $content = [];
}
```

#### CRUD Controller
```php
class ArticleCrudController extends AbstractCrudController
{
    // Add the custom form theme
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->addFormTheme('@EasyEditor/form/editor_widget.html.twig')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield EasyEditorField::new('content');
    }
}
```

#### Twig template
```php
{% for block in object.content %}
    {{ easy_editor_block(block) }}
{% endfor %}
```

### Create a new type

```bash
bin/console make:block
```

### Events

#### easy_editor.render_block
```php
use Symfony\Contracts\EventDispatcher\Event;

$dispatcher->addListener('easy_editor.render_block', function (Event $event) {
    // will be executed when the easy_editor.render_block event is dispatched

    // Get
    $block = $event->getArgument('block');
    $settings = $event->getArgument('settings');

    // Set
    $event->setArgument("block", $block);
    $event->setArgument("settings", $settings);
});
```

## License

[MIT](https://choosealicense.com/licenses/mit/)


## Authors

- [@arnaud-ritti](https://github.com/arnaud-ritti)


