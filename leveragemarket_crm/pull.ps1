param(
    [string]$commitMessage
)

git add .
git commit -m "$commitMessage"
git pull
