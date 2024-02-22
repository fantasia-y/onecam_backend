#!/bin/bash

# Ensure the file is empty.
cat '' > .env.local

# Map the database information from the PLATFORM_RELATIONSHIPS variable into the YAML file.
# Use this process to use whatever variable names your app needs.

printf "%s\n" $(echo $PLATFORM_VARIABLES | base64 --decode | jq -r ".AWS_ACCESS_KEY") >> .env.local
printf "%s\n" $(echo $PLATFORM_VARIABLES | base64 --decode | jq -r ".AWS_ACCESS_SECRET") >> .env.local
printf "%s\n" $(echo $PLATFORM_VARIABLES | base64 --decode | jq -r ".AWS_BUCKET_NAME") >> .env.local
printf "%s\n" $(echo $PLATFORM_VARIABLES | base64 --decode | jq -r ".AWS_REGION") >> .env.local
printf "%s\n" $(echo $PLATFORM_VARIABLES | base64 --decode | jq -r ".AWS_VERSION") >> .env.local
printf "%s\n" $(echo $PLATFORM_VARIABLES | base64 --decode | jq -r ".JWT_PASSPHRASE") >> .env.local
printf "%s\n" $(echo $PLATFORM_VARIABLES | base64 --decode | jq -r ".OAUTH_GOOGLE_ID") >> .env.local
printf "%s\n" $(echo $PLATFORM_VARIABLES | base64 --decode | jq -r ".OAUTH_GOOGLE_SECRET") >> .env.local
printf "%s\n" $(echo $PLATFORM_VARIABLES | base64 --decode | jq -r ".PUSHER_INSTANCE_ID") >> .env.local
printf "%s\n" $(echo $PLATFORM_VARIABLES | base64 --decode | jq -r ".PUSHER_SECRET_KEY") >> .env.local