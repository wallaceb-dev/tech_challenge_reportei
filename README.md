## Setting up your APP locally with Docker

On Linux, in the project root, run the following commands,

```bash
docker-compose up -d
```
```bash
docker exec -it github-explorer-app composer install
```
```bash
docker exec -it github-explorer-app npm run build
```

Then, let's edit .env while creating our OAuth App,

```bash
mv .env.example .env
```
Firstly, change DB_CONNECTION TO **sqlite** and then comment out the other DB related configs,

    DB_CONNECTION=sqlite
    # DB_HOST=127.0.0.1
    # DB_PORT=3306
    # DB_DATABASE=
    # DB_USERNAME=root
    # DB_PASSWORD=

Then, run the following command to create a sqlite file,

```bash
touch database/database.sqlite
```

After that, run the commands below,

```bash
docker exec -it github-explorer-app php artisan key:generate
```
```bash
docker exec -it github-explorer-app php artisan migrate
```

Now, go to [Github OAuth App Creation](https://github.com/settings/applications/new) and then,

 - Give it a name
 - Set ```Homepage URL``` to ```http://localhost:8000```
 - Set ```Authorization callback URL``` to ```http://localhost:8000/auth/github/callback```
 - Submit it
 - Then copy ```Client ID``` and paste it into ```GITHUB_CLIENT_ID inside .env```
 - Generate a new ```Client secret```, copy the newly generated hash and paste it into ```GITHUB_CLIENT_SECRET```, also inside ```.env```
 - Still inside ```.env```, set ```GITHUB_CALLBACK_URI``` to ```http://localhost:8000/auth/github/callback```

 Now access your [localhost](localhost:8000) and you're good to go!