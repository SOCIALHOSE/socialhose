server "192.168.0.110",     :web, :app, :db, :primary => true, :no_release => false
set :deploy_root,           "/var/www/html/new" #project root path on server
set :deploy_to,             "#{deploy_root}/#{deploy_dir}"
set :user,                  "socialhose"
ssh_options[:forward_agent] = true
ssh_options[:port]          = "22"
set :deploy_via,            :rsync_with_remote_cache
set :rsync_options,         "--recursive --delete --delete-excluded --exclude .git* --exclude .build*"
set :controllers_to_clear,  [ "app_dev.php", "app_test.php" ]
set :symfony_env_prod,      "stage"

after "deploy:create_symlink" do
    capifony_pretty_print "--> Creating symlimk for web folder"
    run "sh -c 'rm -rf #{deploy_root}/web && ln -s #{latest_release}/web #{deploy_root}/web'"
    capifony_puts_ok

    capifony_pretty_print "--> Restart workers"
    run "sh -c 'supervisorctl restart documents_email'"
    run "sh -c 'supervisorctl restart documents_fetching'"
    run "sh -c 'supervisorctl restart notification_fetching'"
    run "sh -c 'supervisorctl restart notification_sending'"
    capifony_puts_ok
end
