FROM node:22-alpine

RUN apk add --no-cache tzdata

RUN apk add --no-cache git curl wget vim

WORKDIR /var/www/html/simple-blog-application

CMD ["sh", "-c", "npm install && npm run build"]
