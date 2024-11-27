@servers(["dev" => "$user@$host"])

@task("deploy-dev", ["on" => "dev"])
cd {{ $dir }}

git pull

@endtask
