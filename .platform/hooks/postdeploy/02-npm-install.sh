#!/usr/bin/bash -xe
echo "Installing NPM Packages.."

# Install NPM Packages
cd /var/app/current
npm install

echo "NPM Packages Installed.."

# Run NPM Build
echo "Building Assets.."
npm run build