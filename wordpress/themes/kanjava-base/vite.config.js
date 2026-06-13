import { defineConfig } from 'vite'
import { resolve } from 'node:path'
import { fileURLToPath } from 'node:url'
import { mkdirSync, writeFileSync, rmSync } from 'node:fs'

const __dirname = fileURLToPath(new URL('.', import.meta.url))
const distDir = resolve(__dirname, 'dist')
const hotFile = resolve(distDir, 'hot')

const DEV_PORT = 5173

/**
 * Writes dist/hot while the dev server runs so functions.php can detect dev
 * mode and load assets from the Vite server (HMR). Removed on shutdown.
 */
function wordpressHotFile() {
  return {
    name: 'kanjava-wp-hot-file',
    apply: 'serve',
    configureServer(server) {
      const write = () => {
        mkdirSync(distDir, { recursive: true })
        const { https, port = DEV_PORT } = server.config.server
        writeFileSync(hotFile, `${https ? 'https' : 'http'}://localhost:${port}`)
      }
      const clean = () => {
        try {
          rmSync(hotFile)
        } catch {
          /* already gone */
        }
      }
      server.httpServer?.once('listening', write)
      process.on('exit', clean)
      process.once('SIGINT', () => process.exit())
      process.once('SIGTERM', () => process.exit())
    },
  }
}

export default defineConfig({
  // Assets are referenced through the build manifest in functions.php, so the
  // base only matters for the dev server origin below.
  base: '',
  plugins: [wordpressHotFile()],
  server: {
    host: 'localhost',
    port: DEV_PORT,
    strictPort: true,
    cors: true,
    // Lets url() references inside CSS resolve against the dev server.
    origin: `http://localhost:${DEV_PORT}`,
  },
  build: {
    outDir: 'dist',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: resolve(__dirname, 'src/js/main.js'),
    },
  },
  css: {
    preprocessorOptions: {
      scss: {
        api: 'modern-compiler',
        // Silence Bulma's upstream Dart Sass deprecation warnings.
        quietDeps: true,
      },
    },
  },
})
