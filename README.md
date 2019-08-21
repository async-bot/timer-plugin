# Timer

[![Latest Stable Version](https://poser.pugx.org/async-bot/timer-plugin/v/stable)](https://packagist.org/packages/async-bot/timer-plugin)
[![Build Status](https://travis-ci.org/async-bot/timer-plugin.svg?branch=master)](https://travis-ci.org/async-bot/timer-plugin)
[![Coverage Status](https://coveralls.io/repos/github/async-bot/timer-plugin/badge.svg?branch=master)](https://coveralls.io/github/async-bot/timer-plugin?branch=master)
[![License](https://poser.pugx.org/async-bot/timer-plugin/license)](https://packagist.org/packages/async-bot/timer-plugin)

A very simple timer plugin for the Async Bot framework

The only thing this plugin does is emit a `AsyncBot\Plugin\Event\Tick` event based on a `\DateInterval` interval.

## Requirements

- PHP 7.4

## Installation

```bash
composer require async-bot/timer-plugin
```
