#!/bin/bash

# Ensure the file is empty.
cat '' > .env.local

# Map the database information from the PLATFORM_RELATIONSHIPS variable into the YAML file.
# Use this process to use whatever variable names your app needs.

printf "AWS_ACCESS_KEY=%s\n" $(echo $PLATFORM_VARIABLES | base64 --decode | jq -r ".AWS_ACCESS_KEY") >> .env.local
printf "AWS_ACCESS_SECRET=%s\n" $(echo $PLATFORM_VARIABLES | base64 --decode | jq -r ".AWS_ACCESS_SECRET") >> .env.local
printf "AWS_BUCKET_NAME=%s\n" $(echo $PLATFORM_VARIABLES | base64 --decode | jq -r ".AWS_BUCKET_NAME") >> .env.local
printf "AWS_REGION=%s\n" $(echo $PLATFORM_VARIABLES | base64 --decode | jq -r ".AWS_REGION") >> .env.local
printf "AWS_VERSION=%s\n" $(echo $PLATFORM_VARIABLES | base64 --decode | jq -r ".AWS_VERSION") >> .env.local
printf "JWT_PASSPHRASE=%s\n" $(echo $PLATFORM_VARIABLES | base64 --decode | jq -r ".JWT_PASSPHRASE") >> .env.local
printf "OAUTH_GOOGLE_ID=%s\n" $(echo $PLATFORM_VARIABLES | base64 --decode | jq -r ".OAUTH_GOOGLE_ID") >> .env.local
printf "OAUTH_GOOGLE_SECRET=%s\n" $(echo $PLATFORM_VARIABLES | base64 --decode | jq -r ".OAUTH_GOOGLE_SECRET") >> .env.local
printf "PUSHER_INSTANCE_ID=%s\n" $(echo $PLATFORM_VARIABLES | base64 --decode | jq -r ".PUSHER_INSTANCE_ID") >> .env.local
printf "PUSHER_SECRET_KEY=%s\n" $(echo $PLATFORM_VARIABLES | base64 --decode | jq -r ".PUSHER_SECRET_KEY") >> .env.local