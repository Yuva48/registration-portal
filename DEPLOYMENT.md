# Heroku Deployment Instructions

## Prerequisites
1. Install Heroku CLI: https://devcenter.heroku.com/articles/heroku-cli
2. Install Git: https://git-scm.com/downloads
3. Create Heroku account: https://signup.heroku.com/

## Deployment Steps

### 1. Initialize Git Repository
```bash
git init
git add .
git commit -m "Initial commit - Registration Portal"
```

### 2. Create Heroku App
```bash
heroku create your-registration-portal
```

### 3. Set Environment Variables (Optional)
```bash
heroku config:set APP_ENV=production
heroku config:set APP_DEBUG=false
```

### 4. Deploy to Heroku
```bash
git push heroku main
```

### 5. Open Your App
```bash
heroku open
```

## Important Notes

- Your app will be available at: https://your-registration-portal.herokuapp.com
- The `public/` directory contains all web-accessible files
- File uploads are stored locally (will reset on dyno restart)
- For persistent file storage, consider using Heroku add-ons like AWS S3
- Email functionality requires SMTP configuration

## Troubleshooting

### View Logs
```bash
heroku logs --tail
```

### Check App Status
```bash
heroku ps
```

### Restart App
```bash
heroku restart
```

## Production Considerations

1. **Database**: Consider upgrading to Heroku Postgres for persistent data storage
2. **File Storage**: Use AWS S3 or Cloudinary for file uploads
3. **Email**: Configure SendGrid or similar email service
4. **SSL**: Heroku provides SSL certificates automatically
5. **Custom Domain**: Add your custom domain in Heroku dashboard

## Support

- Heroku Documentation: https://devcenter.heroku.com/
- PHP on Heroku: https://devcenter.heroku.com/articles/getting-started-with-php