# API & Routing

## Routing Architecture

All client-facing routes are wrapped inside a locale matcher middleware in `routes/web.php`.

### Route Prefixes

- Dynamic localized routes example: `/{locale}/credits` routes to the `CreditsController` while mapping translations cleanly before controller execution.
- Valid locales are `en`, `ru`, and `tg`.
- The system prevents missing locale 404s by forcing redirect patterns to default locales or injecting them conditionally.

## Client-Side State & SSR

Data hydration is completely invisible to the frontend. States are injected from Laravel Controllers directly into Vue Pages as raw reactive props via Inertia (`Inertia::render()`).

State mutations run through Inertia forms handling JSON/FormData headers implicitly.

## Third-Party Scripts & API Exts

- The exchange module inherently reaches out via a Scheduled Command or standard controller ping to fetch current NBT (National Bank) metrics or customized JSON sources.
- Maps require interaction with raw coordinate JSON data dumped to the client from the backend.
- Use `npm run lint` and `npm run format` (managed by ESLint/Prettier) when adjusting these APIs.
