server "34.228.99.0", :web, :app, :db, :primary => true, :no_release => false
set :deploy_root,       "/var/www/socialhose/" #project root path on server
set :deploy_to,         "#{deploy_root}/#{deploy_dir}"
set :user,              "deploy"
set :branch,            "master"
set :webserver_user,    "nginx"
set :controllers_to_clear, ['app_dev.php', 'app_test.php', 'app_stage.php']
set :symfony_env_prod, "prod"
set :deploy_via,     :rsync_with_remote_cache
set :rsync_options,  "--recursive --delete --delete-excluded --exclude .git* --exclude .build*"

after "deploy:create_symlink" do
    capifony_pretty_print "--> Creating symlimk for web folder"
    run "sh -c 'rm -rf #{deploy_root}/web && ln -s #{latest_release}/web #{deploy_root}/web'"
    capifony_puts_ok

    capifony_pretty_print "--> Restart workers"
    run "sh -c 'supervisorctl restart documents_email:*'"
    run "sh -c 'supervisorctl restart documents_fetching:*'"
    run "sh -c 'supervisorctl restart notification_fetching:*'"
    run "sh -c 'supervisorctl restart notification_sending:*'"
    capifony_puts_ok
end
