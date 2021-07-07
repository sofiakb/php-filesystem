[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]

[comment]: <> ([![LinkedIn][linkedin-shield]][linkedin-url])



<!-- PROJECT LOGO -->
<br />
<p align="center">

  <h1 align="center">php-filesystem</h1>

  <p align="center">
      A PHP Library for manage files !
      <br />
      <!--<a href="https://github.com/sofiakb/php-filesystem"><strong>Explore the docs »</strong></a>-->
      <br />
      <br />
      <a href="https://github.com/sofiakb/php-filesystem/issues">Report Bug</a>
      ·
      <a href="https://github.com/sofiakb/php-filesystem/issues">Request Feature</a>
  </p>

</p>



<!-- TABLE OF CONTENTS -->
<details open="open">
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About the library</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#roadmap">Roadmap</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#acknowledgements">Acknowledgements</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->

## About The Library

The library allows to file in PHP project.

### Built With

This section should list any major frameworks that you built your project using. Leave any add-ons/plugins for the
acknowledgements section. Here are a few examples.

* [PHP](https://php.net)

<!-- GETTING STARTED -->

### Prerequisites

- php >= 7.4
- ext-fileinfo

### Installation

```shell
composer require sofiakb/php-filesystem
```

<!-- USAGE EXAMPLES -->

## Usage

```php

use Sofiakb\Filesystem\Facades\File;

File::exists('/path/to/file');

```

<!-- ROADMAP -->

## Roadmap

See the [open issues](https://github.com/sofiakb/php-filesystem/issues) for a list of proposed features (and known issues).


<!-- LICENSE -->

## License

Distributed under the MIT License. See `LICENSE` for more information.




<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->

[contributors-shield]: https://img.shields.io/github/contributors/sofiakb/php-filesystem.svg?style=for-the-badge

[contributors-url]: https://github.com/sofiakb/php-filesystem/graphs/contributors

[forks-shield]: https://img.shields.io/github/forks/sofiakb/php-filesystem.svg?style=for-the-badge

[forks-url]: https://github.com/sofiakb/php-filesystem/network/members

[stars-shield]: https://img.shields.io/github/stars/sofiakb/php-filesystem.svg?style=for-the-badge

[stars-url]: https://github.com/sofiakb/php-filesystem/stargazers

[issues-shield]: https://img.shields.io/github/issues/sofiakb/php-filesystem.svg?style=for-the-badge

[issues-url]: https://github.com/sofiakb/php-filesystem/issues

[license-shield]: https://img.shields.io/github/license/sofiakb/php-filesystem.svg?style=for-the-badge

[license-url]: https://github.com/sofiakb/php-filesystem/blob/main/LICENSE

[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555

[linkedin-url]: https://www.linkedin.com/in/sofiane-akbly/