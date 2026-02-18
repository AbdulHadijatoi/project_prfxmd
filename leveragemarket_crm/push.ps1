param(
    [string]$commitMessage
)
git add .
git commit -m "$commitMessage"
$pull = Read-Host "Do you want to PULL? (y/n)"
if ($pull -eq "y") {
    git pull
}
$push = Read-Host "Do you want to PUSH? (y/n)"
if ($push -eq "y") {
    git push
}
