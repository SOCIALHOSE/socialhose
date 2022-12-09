# SOCIALHOSE - Front-end

This folder is the front-end codebase for SOCIALHOSE.

## Requirements

- node `>=5.0.0`
- npm `^3.0.0`

## Overview

The project is using React 16.13.1 which supports hooks but due to early versions of ESLint and Webpack, it may not support few things.

The frontend design follows the [ArchitectUI](https://dashboardpack.com/theme-details/architectui-dashboard-react-pro). Here are the [downloadable ZIP files](<(https://github.com/melzubeir/socialhose/issues/59#issuecomment-702164269)>) which also contain the design for RTL language.

There is also Admin portal to manage users but it is not the part of this folder or React.

## Getting Started

This short guide will help you get started with setting this project up on your development machine.

| Command         | Description                                         |
| --------------- | --------------------------------------------------- |
| `npm start`     | Start development server on `http://localhost:5085` |
| `npm run build` | Create a build for the production at `/web/dist`    |

If we want to make a build locally for the first time, then follow the instructions given in `README.md` located at project's root directory (under Docker heading) which will generate a build in `/web/dist` and it will be served on `http://localhost:8081/`.

Whereas in development, one has to follow the above steps for very first time and then use `npm run start` to start development server on `http://localhost:5085`.

---

**Note:**
Resolve or disable ESLint errors before creating the build. If there are any remaining ESlint errors are there then it will fail to generate the build. (if there are any errors/warnings, then `npm start` will show while server is running)

---
