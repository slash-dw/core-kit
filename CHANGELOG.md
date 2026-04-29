# Changelog

All notable changes to this package are documented in this file following [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Unreleased]

### Added

- MIT license, Larastan (PHPStan level 8), Pint, CI, and `composer` scripts (`lint`, `analyse`, `test`, `ci`).
- Canonical API envelope primitives: `ApiSuccessResponse`, `ApiErrorResponse`, `ApiMeta`, and `ApiErrorItem`.
- `ApiResponses` trait helper: `noContentJson()`.

### Changed

- `ApiResponseFactory` now emits the global envelope contract:
  - success: `{data, meta}`
  - error: `{errors, meta}`
- Pagination details moved under `meta.pagination`.
- Trace id and timestamp now live under `meta`.
