# Stage 1: Build the Vue application
FROM node:18-alpine as builder

WORKDIR /app

COPY package*.json ./
# For PWA, ensure dependencies are installed
RUN npm install

COPY . .
# For PWA, ensure build command runs successfully
RUN npm run build

# Stage 2: Serve the application with Nginx
FROM nginx:alpine

# Copy built files from builder stage
COPY --from=builder /app/dist /usr/share/nginx/html

# Copy PWA Service Worker (generated by vite-plugin-pwa)
# VitePWA automatically puts sw.js in dist, but explicitly copy for clarity
COPY --from=builder /app/dist/sw.js /usr/share/nginx/html/sw.js
COPY --from=builder /app/dist/workbox-*.js /usr/share/nginx/html/

# Expose port 80
EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
