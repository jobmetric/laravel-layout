[contributors-shield]: https://img.shields.io/github/contributors/jobmetric/laravel-layout.svg?style=for-the-badge
[contributors-url]: https://github.com/jobmetric/laravel-layout/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/jobmetric/laravel-layout.svg?style=for-the-badge&label=Fork
[forks-url]: https://github.com/jobmetric/laravel-layout/network/members
[stars-shield]: https://img.shields.io/github/stars/jobmetric/laravel-layout.svg?style=for-the-badge
[stars-url]: https://github.com/jobmetric/laravel-layout/stargazers
[license-shield]: https://img.shields.io/github/license/jobmetric/laravel-layout.svg?style=for-the-badge
[license-url]: https://github.com/jobmetric/laravel-layout/blob/master/LICENCE.md
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-blue.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://linkedin.com/in/majidmohammadian

[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![MIT License][license-shield]][license-url]
[![LinkedIn][linkedin-shield]][linkedin-url]

# Laravel Layout

**Define Layouts Once. Use Everywhere.**

Laravel Layout simplifies the management of page structures and layouts within Laravel applications. Stop duplicating layout logic across controllers and views and start building consistent, reusable layouts with ease. It offers layout definition, binding to applications and pages, plugin positioning, and seamless integration with your models through the `HasLayout` trait. This is where powerful layout management meets developer-friendly simplicity—giving you complete control over how layouts are assigned and resolved without the complexity.

## Why Laravel Layout?

### One Source of Truth

Define layouts once with name, status, and plugin assignments. Reuse the same layout across multiple applications (e.g. shop, cms) and pages (e.g. product, category), or attach it to specific models for fine-grained control. No more scattering layout logic—centralize it in one place.

### Application-Aware and Extensible

Register your applications (shop, cms, admin, etc.) with optional names, descriptions, and default layouts. Bind layouts to application/page pairs or to models via a polymorphic relation. Integrate with Laravel Extension so each layout can specify which plugins appear in which positions. This flexibility ensures your layout system adapts to your application structure.

### Built for Performance

Resolve layout for a model with optional caching (per-minute TTL or forever). Use query scopes to filter models that have a layout for a given application. Events let you react to layout store, update, delete, and relation changes without coupling. Perfect for high-traffic applications where layout resolution must stay fast.

### Simple Model Integration

Attach layouts to any Eloquent model through the `HasLayout` trait. Store one layout per application and optional collection, sync multiple layouts for an application, and fall back to a default or to the layout defined for the application/page. Your models become layout-aware with minimal configuration.

## What is Layout Management?

Layout management is the process of defining, binding, and resolving which layout is used for a given context. In a traditional Laravel application, you might hardcode layout choices in controllers or repeat the same logic in many places. Laravel Layout takes a different approach:

- **Database-Driven**: Layouts are stored in the database with name and status; they can be bound to application/page pairs and to models via relations
- **Application & Page Binding**: Each layout can be associated with one or more application and page combinations (e.g. shop + product page)
- **Plugin Positioning**: Layouts link to extension plugins with position and ordering, so you control where and in what order extensions appear
- **Per-Model Assignment**: Through the `HasLayout` trait, any model can have a layout assigned per application and optional collection, with optional caching and fallbacks

Consider a product that needs a custom layout in the shop application. With Laravel Layout, you can define a "Main" layout with plugins in header and footer, register the "shop" application, bind that layout to the shop product page as a default, and attach a different layout to a specific product via `storeLayout`. The appropriate layout is resolved for that product (or falls back to the application/page or default), with optional caching. The power of layout management lies not only in defining layouts once but also in resolving them consistently for every context—global, per-page, or per-model—throughout your application.

## What Awaits You?

By adopting Laravel Layout, you will:

- **Centralize layout logic** - Define layouts, pages, and plugin positions in one place instead of scattering them across controllers and configs
- **Reuse layouts across applications** - Same layout can serve shop, cms, or admin with different application keys and optional default layouts
- **Attach layouts to any model** - Use the `HasLayout` trait to assign layouts per application/collection, with optional caching and events
- **Stay in control** - Validation rules, API resources, and domain events keep your code consistent and auditable
- **Scale without duplication** - Cache resolved layouts, use registries for applications, and keep migrations and models aligned with Laravel conventions
- **Integrate with extensions** - Position plugins per layout so each page or model can have its own set of extensions in header, footer, or custom positions

## Quick Start

Install Laravel Layout via Composer:

```bash
composer require jobmetric/laravel-layout
```

## Documentation

Ready to transform your Laravel applications? Our comprehensive documentation is your gateway to mastering Laravel Layout:

**[📚 Read Full Documentation →](https://jobmetric.github.io/packages/laravel-layout/)**

The documentation includes:

- **Getting Started** - Quick introduction and installation guide
- **ApplicationRegistry** - Register applications with key, name, description, order, and default layout
- **Layout Service** - CRUD, getApplications, getApplicationsWithOptions, getPosition
- **HasLayout** - Integrate layouts into your models (storeLayout, getLayout, forgetLayout, scopes, caching, fallbacks)
- **Models** - Layout, LayoutPage, LayoutPlugin, LayoutRelation
- **Requests & Resources** - StoreLayoutRequest, UpdateLayoutRequest, API resources
- **Rules & Exceptions** - CheckExistNameRule, ApplicationNotRegisteredException, CollectionPropertyNotExistException
- **Events** - Hook into layout lifecycle and HasLayout relation events
- **Real-World Examples** - See how it works in practice

## Contributing

Thank you for participating in `laravel-layout`. A contribution guide can be found [here](CONTRIBUTING.md).

## License

The `laravel-layout` is open-sourced software licensed under the MIT license. See [License File](LICENCE.md) for more information.
