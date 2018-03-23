# PhileTags

[![Build Status](https://travis-ci.org/Schlaefer/phileTags.svg?branch=master)](https://travis-ci.org/Schlaefer/phileTags)

Tag your pages. Show all pages with that tag.

A [Phile](https://github.com/PhileCMS/Phile) plugin. [Project home](https://github.com/Schlaefer/phileTags).

## Installation

```
composer require siezi/phile-tags;
```

## Activation

```
$config['plugins']['siezi\\phileTags'] = ['active' => true];
```

## Usage

### Add Tags to Pages

Add a new `Tags` attribute to the page meta:

```
/*
Title: My First Blog Post
Tags: js, javascript, php
*/
```

The tags are available as `meta.tags_array` in the template. 

### Shows Tags ###

To show tags for a page and link them to the tag-page:

```twig
{% if meta.tags_array is not empty %}
    {% for tag in meta.tags_array %}
        <a href="{{ base_url }}/tag/{{ tag }}">
            #{{ tag }}
        </a>
    {% endfor %}
{% endif %}

```

### Create Tag Page Template

Create a new template "tag.html" in `themes/<your_theme>/tag.html`. It is used to show all pages having a particular tag when calling  the URL `/tag/<tag-name>`.

```twig
<!DOCTYPE html>
<head>
    <title>{{ meta.title }}</title>
</head>
<body>
    <h2>Posts tagged #{{ current_tag }}:</h2>
    {% for page in pages %}
    {% if page.meta.tags_array and current_tag in page.meta.tags_array %}

        <div class="post">

            <h2><a href="{{ base_url }}/{{ page.url }}">{{ page.meta.title }}</a></h2>
            <div class="excerpt">{{ page.content }}</div>

            <span class="meta-tags">Tags:
            {% for tag in page.meta.tags_array %}
                <a href="{{ base_url }}/tag/{{ tag }}">#{{ tag }}</a>
            {% endfor %}
            </span>

        </div>
    {% endif %}
    {% endfor %}

</body>
</html>
```

## Configuration ##

See the `config.php`.