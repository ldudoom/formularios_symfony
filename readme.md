# Formularios con Symfony 6
***
Lo primero que debemos hacer es instalar el CLI de Symfony, siguiendo las instrucciones de su documentación oficial:

[https://symfony.com/download](https://symfony.com/download)

Una vez instalado el CLI, instalamos un proyecto limpio de Symfony utilizando el comando:

```bash
$ symfony new formularios
```

Una vez que está instalado nuestro proyecto en básico, procedemos a instalar los paquetes necesarios para empezar a trabajar con el framework

```bash
$ composer require symfony/maker-bundle --dev
$ composer require symfony/twig-pack
$ composer require symfony/orm-pack
$ composer require symfony/form
$ composer require symfony/debug-bundle
$ composer require symfony/webpack-encore-bundle
$ npm install
```

<small>_Nota:_ Para habilitar completamente los componentes de front (bootstrap) para formularios vamos a hacer lo siguiente:</small>

1. Instalamos bootstrap en nuestro proyecto, ejecutando:

```bash
$ npm install bootstrap --save-dev
```

2. En el archivo ***assets/styles/app.css*** agregamos:

```css
@import 'bootstrap';
```

3. Luego en el archivo ***config/packages/twig*** agregamos la siguiente línea dentro del bloque _twig_

```yaml
  form_themes: ['bootstrap_5_layout.html.twig']
```

4. Por ultimo, ejecutamos en la consola el comando:

```bash
$ npm run dev
```

Ahora vamos a crear nuestro primer controlador siguiendo estas instrucciones>

- Primero vamos a escribir el comando:

    ```bash
    $ php bin/console make:controller
    ```

- Ahora el asistente nos preguntara por un nombre del controlador, nosotros vamos a colocar el nombre de "**Pages**"

    ```bash
    Choose a name for your controller class (e.g. GrumpyPuppyController):
    > Pages
    ```

- El asistente este momento genera el controlador y una vista por defecto para ser usada por el mismo:

    ```bash
    created: src/Controller/PagesController.php
    created: templates/pages/create_edit.html.twig
    ```

- Lo siguiente será colocar el codigo necesario en el controlador para dejarlo tal como queremos que funcione, para eso, el controlador deberá tener el siguiente código:


***/src/Controller/PagesController.php***
```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{
    #[Route('/contacts-v1', name: 'contact-v1', methods: ['GET', 'POST'])]
    public function contactsV1(): Response
    {
        $form = $this->createFormBuilder()
                     ->add('email', TextType::class)
                     ->add('message', TextareaType::class, [
                         'label' => 'Comentario, sugerencia o mensaje'
                     ])
                     ->add('send', SubmitType::class, [
                         'label' => 'Enviar'
                     ])
                     ->setMethod('GET')
                     ->setAction('otra-url')
                     ->getForm();

        return $this->render('pages/create_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
```

- Ahora vamos a colocar el siguiente código en la vista:

***/templates/pages/contacts.html.twig***
```html
{% extends 'base.html.twig' %}

{% block title %}Contacto!{% endblock %}

{% block body %}
<style>
    .example-wrapper { margin: 1em auto; max-width: 800px; width: 95%; font: 18px/1.5 sans-serif; }
    .example-wrapper code { background: #F5F5F5; padding: 2px 6px; }
</style>

<div class="example-wrapper">
    <h1>Formulario</h1>

    {{ form(form) }}
</div>
{% endblock %}
```

- Como siguiente paso, vamos a configurar el método "contactsV1()" del controlador de la siguiente manera para que reciba los datos del formulario para este ejemplo:

```php
#[Route('/contacts-v1', name: 'contact-v1', methods: ['GET', 'POST'])]
public function contactsV1(Request $request): Response
{
    $form = $this->createFormBuilder()
                 ->add('email', TextType::class)
                 ->add('message', TextareaType::class, [
                     'label' => 'Comentario, sugerencia o mensaje'
                 ])
                 ->add('send', SubmitType::class, [
                     'label' => 'Enviar'
                 ])
                 //->setMethod('GET')
                 //->setAction('otra-url')
                 ->getForm();

    $form->handleRequest($request);
    if( $form->isSubmitted() ) {
        // getData() contiene todos los valores que se han enviado
        dd($form->getData(), $request);
    }

    return $this->render('pages/create_edit.html.twig', [
        'form' => $form->createView(),
    ]);
}
```

## Separando código
***

Ahora vamos a colocar la lógica de la construcción de formularios en una clase separada para poder luego reutilizar este código.

Para esto, lo primero que vamos a hacer es generar un nuevo formulario utilizando el CLI de Symfony de la siguiente manera:

```bash
$ php bin/console make:form
```

El asistente nos solicita un nombre para el formulario, epara este ejemplo vamos a ponerle el nombre de

**Contact**

ya que vamos a gestionar el formulario de contacto.

A continuación el asistente nos pregunta si este formulario va atado a una entidad, ya que este formulario es independiente, no va atado a ninguna entidad, simplemente damos "enter"

El CLI de Symfony genera el siguiente archivo: "_src/Form/ContactType.php_"

***src/Form/ContactType.php***
```php
<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('field_name')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
```

Y ahora lo vamos a utilizar en nuestro desarrollo, para es, importamos esta clase generada en nuestro controlador:

***src/Controller/PagesController.php***
```php
use App\Form\ContactType;
```

Ahora vamos a utilizar este formulario en un nuevo metodo en nuestro controlador, que sera contactV2() y tendrá el siguiente código:

***src/Controller/PagesController.php***
```php
#[Route('/contacts-v2', name: 'contact-v2', methods: ['GET', 'POST'])]
public function contactsV2(Request $request): Response
{
    $form = $this->createForm(ContactType::class);

    $form->handleRequest($request);
    if( $form->isSubmitted() ) {
        dd($form->getData(), $request);
    }

    return $this->render('pages/contact-v2.html.twig', [
        'form' => $form->createView(),
    ]);
}
```

Por ultimo creamos el archivo "***templates/pages/contact-v2.html.twig***" con el siguiente contenido:

***templates/pages/contact-v2.html.twig***
```html
{% extends 'base.html.twig' %}

{% block title %}Contacto!{% endblock %}

{% block body %}
<style>
    .example-wrapper { margin: 1em auto; max-width: 800px; width: 95%; font: 18px/1.5 sans-serif; }
    .example-wrapper code { background: #F5F5F5; padding: 2px 6px; }
</style>

<div class="example-wrapper">
    <h1>Formulario V2</h1>

    {{ form(form) }}
</div>
{% endblock %}

```

Ahora podremos ver el resultado levantando el servidor y revisando la pagina en el navegador:

```bash
$ symfony serve
```

http://localhost:8000/contacts-v2

Lo siguiente es personalizar un poco este formulario que creamos, lo vamos a dejar igual al que tenemos en el controlador, para eso, el archivo de formulario quedará de la siguiente manera

***src/Form/ContactType.php***
```php
<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class)
            ->add('message', TextareaType::class, [
                'label' => 'Comentario, sugerencia o mensaje'
            ])
            ->add('send', SubmitType::class, [
                'label' => 'Enviar'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

```

## Personalizacion HTML
***

Ahora vamos a personalizar nuestro formulario en la vista HTML.

Para eso vamos a seguir los siguientes pasos.

1. En primer lugar vamos a crear un nuevo metodo contactV3() en nuestro controlador y tendra el siguiente codigo:

***src/Controller/PagesController.php***
```php
#[Route('/contacts-v3', name: 'contact-v3', methods: ['GET', 'POST'])]
public function contactsV3(Request $request): Response
{
    $form = $this->createForm(ContactType::class);

    $form->handleRequest($request);
    if( $form->isSubmitted() ) {
        dd($form->getData());
    }

    return $this->render('pages/contact-v3.html.twig', [
        'form' => $form->createView(),
    ]);
}
```

2. Creamos la nueva vista

   ***templates/pages/contact-v3.html.twig***


3. Colocamos el siguiente codigo en esta nueva vista 

***templates/pages/contact-v3.html.twig***
```html
{% extends 'base.html.twig' %}

{% block title %}Contacto!{% endblock %}

{% block body %}
<style>
    .example-wrapper { margin: 1em auto; max-width: 800px; width: 95%; font: 18px/1.5 sans-serif; }
    .example-wrapper code { background: #F5F5F5; padding: 2px 6px; }
</style>

<div class="example-wrapper">
    <h1>Formulario V3</h1>

    {# form(form) #}

    {{ form_start(form, {
    'action': 'procesar',
    'method': 'GET',
    'attr': {
    'novalidate': 'novalidate'
    }
    }) }}

    {{ form_errors(form) }}

    {{ form_row(form.message, {
    'label': 'Comentario o sugerencia',
    'attr': {'maxlenght': 10}
    }) }}

    <div>
        {{ form_errors(form.email) }}
        {{ form_row(form.email) }}
    </div>


    {{ form_end(form) }}

</div>
{% endblock %}
```

De esa manera podemos ir personalizando nuestro formulario, pero existe también de opción de hacerlo de la siguiente manera para tener la configuración original:

***templates/pages/contact-v3.html.twig***
```html
{% extends 'base.html.twig' %}

{% block title %}Contacto!{% endblock %}

{% block body %}
<style>
    .example-wrapper { margin: 1em auto; max-width: 800px; width: 95%; font: 18px/1.5 sans-serif; }
    .example-wrapper code { background: #F5F5F5; padding: 2px 6px; }
</style>

<div class="example-wrapper">
    <h1>Formulario V3</h1>

    {{ form_start(form, {
    'action': 'procesar',
    'method': 'GET',
    'attr': {
    'novalidate': 'novalidate'
    }
    }) }}
    
        {{ form_widget(form) }}
    
    {{ form_end(form) }}

</div>
{% endblock %}
```

Podemos cambiar un tema a los formularios para que un formulario en específico tenga otra apariencia, a parte del estandar que configuramos al inicio, y lo podemos hacer de la siguiente manera:

En la vista, justo antes de abrir la etiqueta de inicio del formulario, agregamos:

    {% form_theme form 'bootstrap_5_horizontal_layout.html.twig' %}

Con lo cual, el archivo completo quedara de la siguiente manera:

***templates/pages/contact-v3.html.twig***
```html
{% extends 'base.html.twig' %}

{% block title %}Contacto!{% endblock %}

{% block body %}
<style>
    .example-wrapper { margin: 1em auto; max-width: 800px; width: 95%; font: 18px/1.5 sans-serif; }
    .example-wrapper code { background: #F5F5F5; padding: 2px 6px; }
</style>

<div class="example-wrapper">
    <h1>Formulario V3</h1>

    {% form_theme form 'bootstrap_5_horizontal_layout.html.twig' %}
    {{ form_start(form, {
    'action': 'procesar',
    'method': 'GET',
    'attr': {
    'novalidate': 'novalidate'
    }
    }) }}
    
        {{ form_widget(form) }}
    
    {{ form_end(form) }}

</div>
{% endblock %}
```

## Mensajes Flash
***

Vamos a configurar los mensajes de feedback que le damos al usuario cuando se ejecuta una determinada accion en el sistema, por ejemplo cuando se almacena un dato en la BBDD, o cuando se elimina, o en este ejemplo, cuando recibimos correctamente los datos del formulario o cuando no se lleno el formulario correctamente:

para lo cual vamos a agregar una linea como esta:

```php
$this->addFlash('success', 'Mensaje de Exito');
```

y luego una redirección.

Vamos entonces a dejar nuestro metodo _contactsV1()_ de _PagesController_ de la siguiente manera:

```php
#[Route('/contacts-v1', name: 'contact-v1', methods: ['GET', 'POST'])]
public function contactsV1(Request $request): Response
{
    $form = $this->createFormBuilder()
                 ->add('email', TextType::class)
                 ->add('message', TextareaType::class, [
                     'label' => 'Comentario, sugerencia o mensaje'
                 ])
                 ->add('send', SubmitType::class, [
                     'label' => 'Enviar'
                 ])
                 ->getForm();

    $form->handleRequest($request);
    if( $form->isSubmitted() ) {
        $this->addFlash('success', 'Prueba formulario #1 con éxito');
        return $this->redirectToRoute('contact-v1');
    }

    return $this->render('pages/create_edit.html.twig', [
        'form' => $form->createView(),
    ]);
}
```

y hacemos lo mismo para los metodos "_contactsV2()_" y "_contactsV3()_"

```php
#[Route('/contacts-v2', name: 'contact-v2', methods: ['GET', 'POST'])]
public function contactsV2(Request $request): Response
{
    $form = $this->createForm(ContactType::class);

    $form->handleRequest($request);
    if( $form->isSubmitted() ) {
        $this->addFlash('primary', 'Prueba formulario #2 con éxito');
        return $this->redirectToRoute('contact-v2');
    }

    return $this->render('pages/contact-v2.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/contacts-v3', name: 'contact-v3', methods: ['GET', 'POST'])]
public function contactsV3(Request $request): Response
{
    $form = $this->createForm(ContactType::class);

    $form->handleRequest($request);
    if( $form->isSubmitted() ) {
        $this->addFlash('info', 'Prueba formulario #3 con éxito');
        return $this->redirectToRoute('contact-v3');
    }

    return $this->render('pages/contact-v3.html.twig', [
        'form' => $form->createView(),
    ]);
}
```

Por ultimo, vamos a configurar las vistas para mostrar estos mensajes, pero lo vamos a hacer en el archivo Base, y queda de la siguiente manera:

***templates/base.html.twig***
```html
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>{% block title %}Welcome!{% endblock %}</title>
        <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 128 128%22><text y=%221.2em%22 font-size=%2296%22>⚫️</text></svg>">
        {# Run `composer require symfony/webpack-encore-bundle` to start using Symfony UX #}
        {% block stylesheets %}
            {{ encore_entry_link_tags('app') }}
        {% endblock %}

        {% block javascripts %}
            {{ encore_entry_script_tags('app') }}
        {% endblock %}
    </head>
    <body>
        <div class="container py-5">
            {# Leer un tipo #}
            {%  for message in app.flashes('success') %}
                <div class="alert alert-success">{{ mensaje }}</div>
            {% endfor %}
            
            {%  for message in app.flashes('primary') %}
                <div class="alert alert-primary">{{ mensaje }}</div>
            {% endfor %}
            
            {%  for message in app.flashes('info') %}
                <div class="alert alert-info">{{ mensaje }}</div>
            {% endfor %}
            
            {% block body %}{% endblock %}
        </div>
    </body>
</html>

```

Podemos mostrar todos los mensajes, no dividirlos por tipo, para hacer nuestro codigo mas eficiente, de la siguiente manera:

```html
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>{% block title %}Welcome!{% endblock %}</title>
        <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 128 128%22><text y=%221.2em%22 font-size=%2296%22>⚫️</text></svg>">
        {# Run `composer require symfony/webpack-encore-bundle` to start using Symfony UX #}
        {% block stylesheets %}
            {{ encore_entry_link_tags('app') }}
        {% endblock %}

        {% block javascripts %}
            {{ encore_entry_script_tags('app') }}
        {% endblock %}
    </head>
    <body>
        <div class="container py-5">
            {# leer todos los mensajes #}
            {% for type, messages in app.flashes %}
                {%  for message in messages %}
                    <div class="alert alert-{{ type }}">{{ message }}</div>
                {% endfor %}
            {% endfor %}

            {% block body %}{% endblock %}
        </div>
    </body>
</html>
```

## Optimizando plantilla TWIG
***

Vamos a optimizar nuestra plantilla Twig dividiendola en archivos pequeños para poder reutilizarlos y organizar mejor el código.
Para eso, lo primero que vamos a hacer es crear 2 nuevos archivos dentro del directorio "templates" que son:

- _alert.html.twig
- _nav.html.twig

Y el contenido de cada archivo va a ser:

**_templates/\_alert.html.twig_**
```html
{# leer todos los mensajes #}
{% for type, messages in app.flashes %}
    {%  for message in messages %}
        <div class="alert alert-{{ type }}">{{ message }}</div>
    {% endfor %}
{% endfor %}
```

Con lo cual, este archivo tendrá el código que teniamos para mostar mensajes al usuario.


**_templates/\_nav.html.twig_**
```html
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow">
    <div class="container">
        <a class="navbar-brand" href="{{ path('index') }}">Symfony/form</a>

        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="{{ path('contact-v1') }}">Contacto #1</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ path('contact-v2') }}">Contacto #2</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ path('contact-v3') }}">Contacto #3</a>
            </li>
        </ul>
    </div>
</nav>
```

Con lo cual tenemos un menu de navegacion en nuestra aplicacion para movernos entre los 3 formularios que hemos construido.

Ahora nuestro archivo base quedará con el siguiente código:

**_templates/base.html.twig_**
```html
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>{% block title %}Welcome!{% endblock %}</title>
        <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 128 128%22><text y=%221.2em%22 font-size=%2296%22>⚫️</text></svg>">
        {# Run `composer require symfony/webpack-encore-bundle` to start using Symfony UX #}
        {% block stylesheets %}
            {{ encore_entry_link_tags('app') }}
        {% endblock %}

        {% block javascripts %}
            {{ encore_entry_script_tags('app') }}
        {% endblock %}

        <style>
            .example-wrapper { margin: 1em auto; max-width: 800px; width: 95%; font: 18px/1.5 sans-serif; }
            .example-wrapper code { background: #F5F5F5; padding: 2px 6px; }
        </style>
    </head>
    <body>
        {% include '_nav.html.twig' %}
        <div class="container py-5">
            {% include '_alert.html.twig' %}

            <div class="example-wrapper">
                {% block body %}{% endblock %}
            </div>
        </div>
    </body>
</html>
```

Ahora nuestro archivo base hace una inclusión de los 2 archivos anteriores, y ya no tenemos el código completo aquí

Por ultimo, ya que modificamos nuestro archivo base, vamos tambien a simplificar nuestros archivo twig que contienen los formularios:


**_templates/pages/contact-v1.html.twig_**
```html
{% extends 'base.html.twig' %}

{% block title %}Contacto!{% endblock %}

{% block body %}
    <h1>Formulario V1</h1>

    {{ form(form) }}
{% endblock %}

```

**_templates/pages/contact-v2.html.twig_**
```html
{% extends 'base.html.twig' %}

{% block title %}Contacto!{% endblock %}

{% block body %}
    <h1>Formulario V2</h1>

    {{ form(form) }}
{% endblock %}

```

**_templates/pages/contact-v3.html.twig_**
```html
{% extends 'base.html.twig' %}

{% block title %}Contacto!{% endblock %}

{% block body %}
    <h1>Formulario V3</h1>

    {# form(form) #}

    {% form_theme form 'bootstrap_5_horizontal_layout.html.twig' %}
    {{ form_start(form, {
        'method': 'POST',
        'attr': {
            'novalidate': 'novalidate'
        }
    }) }}

        {{ form_widget(form) }}

    {{ form_end(form) }}
{% endblock %}
```

## Relacionando formularios con entidades
***

En primer lugar vamos a crear una entidad utilizando el CLI de Symfony de la siguiente manera:

```bash
$ php bin/console make:entity
```

En este momento inicia el asistente para generar una entidad, en primer lugar nos pregunta cual es el nombre que vamos a colocarle a nuestra entidad y colocamos "Post"


```bash
 Class name of the entity to create or update (e.g. FierceElephant):
 > Post
Post

 created: src/Entity/Post.php
 created: src/Repository/PostRepository.php
 
 Entity generated! Now let's add some fields!
 You can always add more fields later manually or by re-running this command.
```

Ahora si continuamos con el asistente, y vamos a crear una entidad que tiene 2 campos: title y body y con la siguiente configuración:

```bash
New property name (press <return> to stop adding fields):
 > title

 Field type (enter ? to see all types) [string]:
 > string


 Field length [255]:
 > 255

 Can this field be null in the database (nullable) (yes/no) [no]:
 > no

 updated: src/Entity/Post.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 > body

 Field type (enter ? to see all types) [string]:
 > text
text

 Can this field be null in the database (nullable) (yes/no) [no]:
 > no   

 updated: src/Entity/Post.php

# Ya no necesitamos mas campos por lo que este momento presionamos "Enter"
 Add another property? Enter the property name (or press <return> to stop adding fields):
 >

           
  Success! 
           

 Next: When you're ready, create a migration with php bin/console make:migration
```

Con esa configuración, nuestra entidad Post queda creada con el siguiente codigo:

***/src/Entity/Post.php***
```php
<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $body = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }
}

```

Adicionalmente queda generado el repositorio con el siguiente codigo:

***/src/Repository/PostRepository.php***
```php
<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function save(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Post[] Returns an array of Post objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Post
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

```

Lo siguiente es vincular esta entidad a un formulario, para eso creamos el nuevo formulario utilizando el CLI de Symfony:

```bash
$ php bin/console make:form 
```

Ahora inicia el asistente para la generacion del formulario, veremos algo como esto:

```bash
The name of the form class (e.g. VictoriousChefType):
 > Post    

 The name of Entity or fully qualified model class name that the new form will be bound to (empty for none):
 > Post
Post

 created: src/Form/PostType.php

           
  Success! 
           

 Next: Add fields to your form and start using it.
 Find the documentation at https://symfony.com/doc/current/forms.html
```

El resultado es el formulario con el siguiente codigo:

***/src/Form/PostType.php***

```php
<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('body')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
```

El siguiente paso es generar el controlador, para eso ejecutamos en la consola:

```bash
$ php bin/console make:controller

 Choose a name for your controller class (e.g. VictoriousPopsicleController):
 > Post

 created: src/Controller/PostController.php
 created: templates/post/create_edit.html.twig

           
  Success! 
           

 Next: Open your new controller class and add some pages!
```

Y en el controlador importamos la clase PostType y lo dejamos configurado de la siguiente manera:

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Form\PostType;

class PostController extends AbstractController
{
    #[Route('/post/create', name: 'app_post_create')]
    public function index(): Response
    {
        $form = $this->createForm(PostType::class);

        return $this->render('post/create_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

```

Por ultimo, colocamos el siguiente código en la vista:

***/src/templates/Post/create.html.twig***
```html
{% extends 'base.html.twig' %}

{% block title %}Create Post!{% endblock %}

{% block body %}
    <div class="example-wrapper">
        <h1>Crear Post</h1>

        {{ form(form) }}
    </div>
{% endblock %}
```

## Tipos de controles y CSRF
***

Lo primero que vamos a hacer es habilitar la protección CSRF en la configuración del framework.

Para eso, vamos al archivo ***/config/packages/framework.yaml***, y descomentamos la siguiente linea

```yaml
framework:
    csrf_protection: true
```

luego de eso, deberemos instalar la siguiente librería:

```bash
$ composer require symfony/security-csrf
```

Por último, vamos a personalizar un poco el formulario colocando el siguiente código:

***/src/Form/PostType.php***

```php
<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Título del Post',
                'help' => 'Piensa en el SEO. Cómo buscarias en Google?'
            ])
            ->add('body', TextareaType::class, [
                'label' => 'Contenido del Post',
                'attr' => [
                    'rows' => 10,
                    'class' => 'bg-light'
                ],
            ])
            ->add('send', SubmitType::class, [
                'label' => 'Guardar Post',
                'attr' => [
                    'class' => 'btn-success btn-lg'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            //'csrf_protection' => false, // De esta manera podemos eliminar la protección de éste formulario
        ]);
    }
}
```

## Control de Elección (ComboBox)
***

Vamos a crear una lista de selección en el formulario, por ejemplo si quisieramos seleccionar la categoría a la cual pertenece el Post que estamos creando.

Trabajamos en la clase donde tenemos la configuración del formulario (***/src/Form/PostType.php***).

Lo primero que vamos a hacer es importar la clase correspondiente:

```php
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
```

Ahora vamos a agregar el componente en nuestro formulario de la siguiente manera:

```php
$builder->add('category', ChoiceType::class, [
                'choices' => [
                    'PHP' => 'php',
                    'Laravel' => 'laravel',
                    'Symfony' => 'symfony'
                ],
                'placeholder' => 'Seleccione una ...',
                'label' => 'Categorías',
            ])
```

Y podemos hacer mas detallada la lista de esta manera:

```php
$builder->add('category', ChoiceType::class, [
                'choices' => [
                    'Languages' => [
                        'PHP' => 'php',
                        'JAVA' => 'java',
                        'JAVASCRIPT' => 'javascript'
                    ],
                    'Frameworks' => [
                        'PHP' => [
                            'Laravel' => 'laravel',
                            'Symfony' => 'symfony',
                            'Codeigniter' => 'codeigniter'
                        ],
                        'JAVA' => [
                            'Spring Boot' => 'spring boot'
                        ],
                        'JAVASCRIPT' => [
                            'Serverless' => 'serverless',
                            'Express' => 'express',
                            'NestJS' => 'nestjs',
                            'NextJS' => 'nextjs'
                        ],
                    ],
                ],
                'placeholder' => 'Seleccione una ...',
                'label' => 'Categorías',
            ])
```

## Base de Datos
***

Ahora vamos a relacionar este componente, es decir las categorias de los posts, con una tabla en nuestra BBDD, para eso vamos a necesitar trabajar con una entidad

Para eso vamos a crear una nueva entidad:

```bash
$ php bin/console make:entity
```

Y llenamos el asistente de generacion de nueva entidad con la siguiente informacion:

```bash
Class name of the entity to create or update (e.g. AgreeablePuppy):
 > Category
Category

 created: src/Entity/Category.php
 created: src/Repository/CategoryRepository.php
 
 Entity generated! Now let's add some fields!
 You can always add more fields later manually or by re-running this command.

 New property name (press <return> to stop adding fields):
 > name

 Field type (enter ? to see all types) [string]:
 > string


 Field length [255]:
 > 255

 Can this field be null in the database (nullable) (yes/no) [no]:
 > no

 updated: src/Entity/Category.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 >


           
  Success! 
           

 Next: When you're ready, create a migration with php bin/console make:migration

```

Con esta acción se generaron 2 archivos

- Category.php
- CategoryRepository.php

Y tienen el siguiente código:

- ***/src/Entity/Category.php***
```php
<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}

```


- ***/src/Repository/CategoryRepository.php***
```php
<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function save(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Category[] Returns an array of Category objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Category
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

```

Ahora vamos a trabajar con la relación que hay entre las entidades Post y Category.

En este caso, existe una relación ***"1:m"*** entre Category y Post, es decir, Una categoría puede tener varios posts, y un post puede pertenecerle a una categoría.

En primer lugar, para lograr esto, vamos a tener que alterar nuestra entidad Post de la siguiente manera:

```bash
$ php bin/console make:entity
```

El asistente en este punto nos hace esta pregunta:

```bash
Class name of the entity to create or update (e.g. DeliciousElephant):
 > 
```

Debido a que vamos a hacer una actualización de una entidad, escribimos el nombre de la entidad que vamos a modificar, en nuestro caso **"Post"**

Y a continuación, completamos el asistente de la siguiente manera

```bash
 Class name of the entity to create or update (e.g. DeliciousElephant):
 > Post
Post

 Your entity already exists! So let's add some new fields!

 New property name (press <return> to stop adding fields):
 > category
 
 Field type (enter ? to see all types) [string]:
 > relation
 
 What class should this entity be related to?:
 > Category


 What type of relationship is this?
 ------------ ---------------------------------------------------------------------
  Type         Description
 ------------ ---------------------------------------------------------------------
  ManyToOne    Each Post relates to (has) one Category.
               Each Category can relate to (can have) many Post objects.

  OneToMany    Each Post can relate to (can have) many Category objects.
               Each Category relates to (has) one Post.

  ManyToMany   Each Post can relate to (can have) many Category objects.
               Each Category can also relate to (can also have) many Post objects.

  OneToOne     Each Post relates to (has) exactly one Category.                     
               Each Category also relates to (has) exactly one Post.
 ------------ ---------------------------------------------------------------------

 Relation type? [ManyToOne, OneToMany, ManyToMany, OneToOne]:
 > ManyToOne 
ManyToOne

 Is the Post.category property allowed to be null (nullable)? (yes/no) [yes]:
 > no

 Do you want to add a new property to Category so that you can access/update Post objects from it - e.g. $category->getPosts()? (yes/no) [yes]:
 > no

 updated: src/Entity/Post.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 > 


           
  Success! 
           

 Next: When you're ready, create a migration with php bin/console make:migration
```

Una vez terminado el asistente, podemos ver que el archivo Post.php cambió, y ahora tiene el siguiente código:

```php
<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $body = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}

```

Ahora vamos a configurar nuestra base de datos, a crear las migraciones y ejecutarlas para crear sus correspondientes tablas:

Para esto, primero vamos a colocar las credenciales de nuestra BBDD en el archivo de configuración de entorno ***".env"***

- ***.env***
```dotenv
# Descomentamos la linea que contiene la BBDD que vamos a usar, 
# en este ejemplo vamos a usar Postgres, y debemos asegurarnos
# de que vamos a colocar el numero de versión correcto del DBMS que vamos a usar.
# Ahora colocamos nuestras credenciales, y vamos a 
# colocar como nombre de la BBDD "symfony_forms"
DATABASE_URL="postgresql://postgres:postgres@localhost:5432/symfony_forms?serverVersion=15&charset=utf8"
```

Ahora vamos a crear la BBDD usando los comandos de Doctrine de la siguiente manera

```bash
$ php bin/console doctrine:database:create
```

A continuación generamos nuestros archivos de migración para generar las tablas de nuestra BBDD:

```bash
$ php bin/console make:migration
           
  Success! 
           
 Next: Review the new migration "migrations/Version20230317171414.php"
 Then: Run the migration with php bin/console doctrine:migrations:migrate
 See https://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html

```

Por último, ejecutamos el comando que nos sugiere Symfony para crear las tablas en la BBDD

```bash
$ php bin/console doctrine:migrations:migrate

WARNING! You are about to execute a migration in database "symfony_forms" that could result in schema changes and data loss. Are you sure you wish to continue? (yes/no) [yes]:
 > yes

[notice] Migrating up to DoctrineMigrations\Version20230317171414
[notice] finished in 34.5ms, used 14M memory, 1 migrations executed, 6 sql queries
                                                                                                                        
 [OK] Successfully migrated to version : DoctrineMigrations\Version20230317171414                                       
                                                                                                                        


```


## Control de Elección y Tabla
***

Ahora vamos a hacer que nuestro control de selección (combo box) tome la información de la tabla "category" y que ya no esten quemadas las opciones en el código fuente:

para eso, lo primero que vamos a hacer es lo siguiente:

1. Abrimos nuestro archivo de formulario ***"PostType.php"***
2. Colocamos el siguiente código en el archivo:

Reemplazamos la línea:
```php
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
```

por la linea
```php
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
```

De esta manera queda importada la clase correcta para manejar los datos del combo box utilizando una entidad y por tanto una tabla de nuestra BBDD

Ahora, reemplazamos el siguiente bloque de código
```php
->add('category', ChoiceType::class, [
            'choices' => [
                'Languages' => [
                    'PHP' => 'php',
                    'JAVA' => 'java',
                    'JAVASCRIPT' => 'javascript'
                ],
                'Frameworks' => [
                    'PHP' => [
                        'Laravel' => 'laravel',
                        'Symfony' => 'symfony',
                        'Codeigniter' => 'codeigniter'
                    ],
                    'JAVA' => [
                        'Spring Boot' => 'spring boot'
                    ],
                    'JAVASCRIPT' => [
                        'Serverless' => 'serverless',
                        'Express' => 'express',
                        'NestJS' => 'nestjs',
                        'NextJS' => 'nextjs'
                    ],
                ],
            ],
            'placeholder' => 'Seleccione una ...',
                            'label' => 'Categorías',
])
```

Por este
```php
->add('category', EntityType::class, [
                'class' => Category::class,
                'placeholder' => 'Seleccione una ...',
                'label' => 'Categorías',
            ])
```

Y en este momento el sistema fallará con el error:

```html
Object of class App\Entity\Category could not be converted to string
```

Para corregir esto, hacemos lo siguiente:

1. Abrimos nuestra entidad de categorias ***"/src/Entity/Category.php"***
2. Agregamos el siguiente método al final de la clase:

```php
public function __toString(): string{
    return $this->getName();
}
```

## Guardando Datos
***
Ahora vamos a guardar en nuestra BBDD los posts que generemos desde el formulario.

Para eso vamos a necesitar modificar nuestro controlador ***"/src/Controller/PostController.php"***.

1. Primero agregamos la clase que nos ayuda a procesar la solicitud que llega desde la vista:

```php
use Symfony\Component\HttpFoundation\Request;
```

Vamos tambien a necesitar al administrador de entidades

```php
use Doctrine\ORM\EntityManagerInterface;
```

Ahora realizamos la inyección de estas dependencias en el metodo crear:

```php
#[Route('/post/create', name: 'app_post_create')]
public function create(Request $request, EntityManagerInterface $entityManager): Response
{
...

```

A continuación, modificamos el cuerpo de este método de la siguiente manera:

```php
#[Route('/post/create', name: 'app_post_create')]
public function create(Request $request, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(PostType::class);

    // Iniciamos el manejador de la solicitud
    $form->handleRequest($request);
    // Verificamos si el formulario fue enviado
    if( $form->isSubmitted() ) {
        // Persistimos la data recibida en la solicitud
        $entityManager->persist($form->getData());
        // Finalmente guardamos en la BBDD
        $entityManager->flush();
        // Configuramos un mensaje y devolvemos al usuario a la pantalla
        // del formulario junto con el mensaje de Post Guardado
        $this->addFlash('success', 'Post almacenado con éxito');
        return $this->redirectToRoute('app_post_create');
    }
    
    // En caso de que el formulario no haya sido enviado, simplemente
    // mostramos el formulario junto con el mensaje flash que se haya enviado
    return $this->render('post/create_edit.html.twig', [
        'form' => $form->createView(),
    ]);
}
```

## Editando Datos
***

Ahora vamos a ver como editar un articulo previamente almacenado

Primero vamos a importar la entidad Post, necesaria para hacer esta actualizacion

```php
use App\Entity\Post;
```
Ahora vamos a crear el metodo de edicion de Post en nuestro controlador con el siguiente codigo:

```php
#[Route('/post/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
public function edit(Post $post, Request $request, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(PostType::class, $post);

    $form->handleRequest($request);
    if( $form->isSubmitted() ) {
        $entityManager->flush();
        $this->addFlash('success', 'Post actualizado con éxito');
        return $this->redirectToRoute('app_post_edit', ['id' => $post->getId()]);
    }

    return $this->render('post/edit.html.twig', [
        'form' => $form->createView(),
    ]);
}
```

La diferencia de este metodo, con respecto al metodo de creacion de Post, en primer lugar es la ruta:

Agregamos un valor variable que es el ID del post, cambiamos la uri y actualizamos el nombre

```php
#[Route('/post/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
```

Lo siguiente es la inyección de la dependencia del Post como tal:

```php
public function edit(Post $post, Request $request, EntityManagerInterface $entityManager): Response
{
 ...
```
Dentro del cuerpo del metodo, ahora construimos el formulario utilizando el post que nos llega como dependencia en el metodo:
```php
public function edit(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class, $post);
    ...
```

Ahora, en el cuerpo del metodo, eliminamos la linea de persistencia, debido a que Doctrine ya esta mapeando el Post gracias a la inyección de dependencia que hicimos, por lo que unicamente es necesario guardar la información en la BBDD, por lo que el unico cambio necesario es eliminar esa linea, actualizar el mensaje y actualizar el nombre de la ruta en la redirección y agregamos el ID del post:

```php
$form->handleRequest($request);
if( $form->isSubmitted() ) {
    $entityManager->flush();
    $this->addFlash('success', 'Post actualizado con éxito');
    return $this->redirectToRoute('app_post_edit', ['id' => $post->getId()]);
}
```

Para terminar la edición del metodo de edicion en el controlador, unicamente cambiamos el nombre de la vista al momento de renderizarla, para que se cargue la vista **"edit"** en lugar de la **"create"**

```php
return $this->render('post/edit.html.twig', [
    'form' => $form->createView(),
]);
```

Ahora es momento de crear la vista de edición de Posts.

Para eso, creamos el archivo ***/src/templates/post/edit.html.twig*** y colocamos el siguiente codigo:

```html
{% extends 'base.html.twig' %}

{% block title %}Edit Post!{% endblock %}

{% block body %}
<div class="example-wrapper">
    <h1>Editar Post</h1>
    <hr>

    {{ form(form) }}
</div>
{% endblock %}

```

***Nota:*** En caso de recibir un error al momento de cargar la pantalla del formulario de edicion de posts, puede deberse a que hace falta la instalación de un vendor, si ese es el caso, instalamos la siguiente dependencia:

```bash
$ composer require sensio/framework-extra-bundle
```

## Validacion de Datos
***

Lo primero que vamos a hacer es instalar el componente que nos ayuda a validar los datos de los formularios del lado del backend, ya que desde el front ya se está validando, pero vamos a agregar una capa adicional de validación de datos

```bash
$ composer require symfony/validator
```

Este momento ya procedemos a agregar el código que nos permita realizar nuestras validaciones.

Primero, vamos a modificar el codigo de la entidad Post.php, y dejamos el archivo de la siguiente manera:

1. Agregamos la clase de validación

***/src/Entity/Post.php***
```php
use Symfony\Component\Validator\Constraints as Assert;
```

2. Agregamos las validaciones en los atributos "title", "body" y "category"

***/src/Entity/Post.php***
```php
#[ORM\Column(length: 255)]
#[Assert\NotBlank]
#[Assert\Length(min:9,max:90)]
private ?string $title = null;

#[ORM\Column(type: Types::TEXT)]
#[Assert\NotBlank]
private ?string $body = null;

#[ORM\ManyToOne]
#[ORM\JoinColumn(nullable: false)]
#[Assert\NotNull]
private ?Category $category = null;
```

3. En el controlador, debemos modificar, tanto en el metodo ***create()*** como el el ***edit()***:

***/src/Controller/PostController.php***

La línea:
```php
if( $form->isSubmitted() ) {
...
```

Cambiarla por:
```php
if( $form->isSubmitted() && $form->isValid()) {
...
```

***NOTA:*** Para ver las validaciones aplicadas en una entidad, por ejemplo la Entidad ***/src/Entity/Post.php*** se puede utilizar el siguiente comando:

```bash
$ symfony console debug:validator "App\Entity\Post" 
```

El resultado de ese comando es algo como esto:

```bash
App\Entity\Post
---------------

+----------+--------------------------------------------------+---------------+----------------------------------------------------------------------------------+
| Property | Name                                             | Groups        | Options                                                                          |
+----------+--------------------------------------------------+---------------+----------------------------------------------------------------------------------+
|          |                                                  |               |   "payload" => null
|          |                                                  |               | 8m                                                                               |
|          |                                                  |               | ]                                                                                |
| category | Symfony\Component\Validator\Constraints\NotNull  | Default, Post | [                                                                                |
|          |                                                  |               |   "message" => "This value should not be null.",                                 |
|          |                                                  |               |   "payload" => null                                                              |
|          |                                                  |               | ]                                                                                |
+----------+--------------------------------------------------+---------------+----------------------------------------------------------------------------------+
```

Esta configuración, funciona para el metodo de crear Posts, pero para editar, vamos a tener errores, para corregirlos, debemos hacer las siguientes modificaciones:

En la entidad, hacemos la siguiente modificación.

***/src/Entity/Post.php***
Reemplazamos estas lineas 
```php
public function setTitle(string $title): self
...

public function setBody(string $body): self
...
```

Por estas lineas
```php
public function setTitle(string $title = null): self
...

public function setBody(string $body = null): self
...
```

***NOTA:*** Con la categoría no es necesario porque ya tiene una validación, agregando el simbolo ***"?"*** de esta manera:
```php
public function setCategory(?Category $category): self
...
```

Es decir, podriamos, a los metodos setTitle() y setBody(), colocarlos de las siguientes maneras:
1. Opcion 1
```php
public function setTitle(string $title = null): self
...

public function setBody(string $body = null): self
...
```
2. Opcion 2
```php
public function setTitle(null|string $title): self
...

public function setBody(null|string $body): self
...
```

3.Opcion 3
```php
public function setTitle(?string $title): self
...

public function setBody(?string $body): self
...
```

Vamos a dejar nuestro codigo con la "Opcion 3" para que quede uniforme.

## Acceso a Formularios
***

Básicamente vamos a mejorar las vistas, vamos a colocar la lista de posts en el index del proyecto, y accesos hacia el formulario de creación y de edición de posts

1. En el controlador de paginas importamos el EntityManager y la entidad Post

***/src/Controller/PagesControler.php***
```php
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
```

2. Modificamos el método index() de la siguiente manera:

```php
#[Route('/', name: 'index', methods: ['GET'])]
public function index(EntityManagerInterface $entityManager): Response
{
    return $this->render('pages/index.html.twig', [
        'posts' => $entityManager->getRepository(Post::class)->findAll()
    ]);
}
```

3. Imprimimos esta lista de Posts en la vista:

***/templates/pages/index.html.twig***
```html
{% extends 'base.html.twig' %}

{% block title %}Contacto!{% endblock %}

{% block body %}
<div class="example-wrapper">
    <h1 class="d-flex align-items-center justify-content-between mb-4">
        Ejemplo Home

        <a href="{{ path('app_post_create') }}" class="btn btn-primary btn-sm">+ Crear Post</a>
    </h1>
    <hr>
    <table class="table table-striped">
        <tbody>
        {% for post in posts %}
        <tr>
            <td>{{ post.title }}</td>
            <td>
                <a href="{{ path('app_post_edit', {id: post.id}) }}">Editar</a>
            </td>
        </tr>
        {% endfor %}
        </tbody>

    </table>
</div>
{% endblock %}
```