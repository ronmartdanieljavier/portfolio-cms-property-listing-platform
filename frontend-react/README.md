# Frontend — React

React 19 frontend for the portfolio CMS property listing platform, built with Vite and TypeScript.

## Tech Stack

| Tool       | Version |
| ---------- | ------- |
| React      | 19      |
| TypeScript | ~6.0    |
| Vite       | 8       |
| ESLint     | 10      |

## Prerequisites

- Node.js 20+
- npm

## Getting Started

```bash
# Install dependencies
npm install

# Start the development server (http://localhost:5173)
npm run dev
```

## Available Scripts

| Script            | Description                          |
| ----------------- | ------------------------------------ |
| `npm run dev`     | Start dev server with HMR            |
| `npm run build`   | Type-check and build for production  |
| `npm run preview` | Preview the production build locally |
| `npm run lint`    | Run ESLint                           |

## Project Structure

```
frontend-react/
├── public/          # Static assets (favicon, icons)
├── src/
│   ├── main.tsx     # Application entry point
│   ├── App.tsx      # Root component
│   ├── App.css      # Root component styles
│   └── index.css    # Global styles
├── index.html
├── vite.config.ts
├── tsconfig.json
├── tsconfig.app.json
└── tsconfig.node.json
```

## ESLint Configuration

The project uses `typescript-eslint` with type-aware linting. To enable stricter rules, update [eslint.config.js](eslint.config.js):

```js
// Replace tseslint.configs.recommended with one of:
tseslint.configs.recommendedTypeChecked; // type-aware
tseslint.configs.strictTypeChecked; // stricter
tseslint.configs.stylisticTypeChecked; // + stylistic
```

And set the parser options:

```js
languageOptions: {
  parserOptions: {
    project: ['./tsconfig.node.json', './tsconfig.app.json'],
    tsconfigRootDir: import.meta.dirname,
  },
},
```

## Related

- [Backend (Laravel)](../backend) — REST API
- [Root README](../README.md) — Full project overview
