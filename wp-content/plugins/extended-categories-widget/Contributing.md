# AVH Extended Categories Contribution Guide

This page contains guidelines for contributing to AVH Extended Categories. Please review these guidelines before submitting any pull requests.

## Getting started
* Make sure you have a [GitHub account](https://github.com/signup/free)

## Submitting an issue

* Submit a ticket for your issue, assuming one does not already exist.
  * Clearly describe the issue including steps to reproduce when it is a bug.
* Make sure you fill in the earliest version that you know has the issue.

## Submitting a feature request
* Submit a ticket for your issue, assuming one does not already exist.
* The title of the ticket should start with `[Request]`
* Make sure you fill in the earliest version that you know has the issue.

## Translating
We have yet to find a good way to organize the translations for the AVH Extended Categories plugin. We have looked at several solutions but never really could decide one which works pretty good. If you have a suggestion please submit an issue and we'll lokk into it.

## For developers

### Getting started
* Fork the repository on GitHub
* We use [git-flow AVH Edition](https://github.com/petervanderdoes/gitflow) for our development.

### Which Branch?

**ALL** bug fixes should be made to the master branch. Bug fixes should never be sent to the `develop` branch unless they fix features that exist only in the upcoming release.

### Pull Requests

The pull request process differs for new features and bugs. Before sending a pull request for a new feature, you should first create an issue with `[Proposal]` in the title. The proposal should describe the new feature, as well as implementation ideas. The proposal will then be reviewed and either approved or denied. Once a proposal is approved, a pull request may be created implementing the new feature. Pull requests which do not follow this guideline will be closed immediately.

Pull requests for bugs may be sent without creating any proposal issue. If you believe that you know of a solution for a bug that has been filed on Github, please leave a comment detailing your proposed fix.

### Coding Guidelines

AVH Extended Categories tries to follow the [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md), [PSR-1](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md) and [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) coding standards.

We understand the code is not fully compliant with the above guidelines and sometimes it can't be, but when coding do follow the guidelines as close as possible.

## Additional Resources

* [General GitHub documentation](http://help.github.com/)
* [GitHub pull request documentation](http://help.github.com/send-pull-requests/)