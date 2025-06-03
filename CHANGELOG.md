# Release notes

## 1.1.1

- Add actions for additional logging and exception handling

## 1.1.0

- Fix bug where failed publishes were not shown correctly in the admin interface
- Update pnpm so prettier works the same locally and in CI

## 1.0.0

Add support for other GitHub organizations then `grrr-amsterdam`.

- Add `GRRR_WORDPRESS_PUBLISH_OWNER` and `GRRR_WORDPRESS_PUBLISH_REPOSITORY` constants
- Remove hard coded grrr-amsterdam GitHub owner connection
- Remove deprecated config vars starting with `GITHUB_DEPLOY_*`

## 0.3.0

- Add scheduled publish support
- Improve error handling

## 0.2.0

- Add git reference support

## 0.1.0

- Initial release
