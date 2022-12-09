# Stages
set :stages,        %w(production demo development staging)
set :default_stage, "development"
set :stage_dir,     "app/config/stage"
set :keep_releases, 3

# Capifony settings
require 'capistrano/ext/multistage'
require 'fileutils'
require 'zlib'

# Application
set :application,   "socialhose" #application name
set :app_path,      "app"
set :web_path,      "web"
set :model_manager, "doctrine"
set :deploy_dir,    "deploy"
set :log_path,      "var/logs"
set :cache_path,    "var/cache"
set :session_path,  "var/sessions"
set :node_modules_path,  "frontend/node_modules"

# Version control
# pass branch as parameter: example: cap development deploy -S branch=origin/development
set :scm,               :git
set :repository,        "git@github.com:melzubeir/socialhose.git" #application repository URL
set :git_shallow_clone, 1
set :branch,            fetch(:branch, '').sub!(/^.*\//, "")

# Directories
set :writable_dirs,         [log_path, cache_path, session_path]
set :shared_files,          ["app/config/parameters.yml"]
set :shared_children,       [log_path, node_modules_path]

# Permissions
set :permission_method,     :acl
set :use_set_permissions,   true
set :use_sudo,              false
set :webserver_user,        "apache"
ssh_options[:forward_agent] = true

# Symfony
set :use_composer,          true
set :composer_options,      "--verbose --prefer-dist --optimize-autoloader --no-progress --no-interaction"
set :update_vendors,        false
set :copy_vendors,          true
set :dump_assetic_assets,   true
set :interactive_mode,      false
set :update_cmd,            "./update.sh" # will run after deploy
set :clear_controllers,     false
set :symfony_env,           "dev"
set :symfony_console,       "bin/console"

# Custom tasks
namespace :deployment do

  desc "Update site config"
  task :update_site_config do
    capifony_pretty_print "--> Update site config"
    run "#{try_sudo} sh -c 'cd #{latest_release} && #{php_bin} #{symfony_console} socialhose:site-settings:sync #{console_options}'"
    capifony_puts_ok
  end

  namespace :update_code do

    desc "Rewrite parameters"
    task :rewrite_params do
      capifony_pretty_print "--> Rewriting parameters.yml with app/config/parameters.yml.#{stage}"
      run "sh -c 'cd #{latest_release} && cp app/config/parameters.yml.#{stage} app/config/parameters.yml'"
      capifony_puts_ok
    end

    desc "Rewrite .htaccess"
    task :rewrite_htaccess do
      capifony_pretty_print "--> Rewriting .htaccess with .htaccess.#{stage}"
      run "sh -c 'cd #{latest_release} && cp web/.htaccess.#{stage} web/.htaccess'"
      capifony_puts_ok
    end

  end

  namespace :frontend do

    desc "Rewrite frontend/app/appConfig.js"
    task :rewrite_config do
      set :file, "frontend/app/appConfig.js"
      capifony_pretty_print "--> Rewriting #{file} with #{file}.#{stage}"
      run "sh -c 'cd #{latest_release} && cp #{file}.#{stage} #{file}'"
      capifony_puts_ok
    end

    desc "Install node modules"
    task :install do
      capifony_pretty_print "--> Install node modules"
      run "sh -c 'cd #{latest_release}/frontend && npm install'"
      capifony_puts_ok
    end

    desc "Build"
    task :build do
      capifony_pretty_print "--> Build forntend"
      run "sh -c 'cd #{latest_release}/frontend && npm run build'"
      capifony_puts_ok
    end

  end

end

# Dependences
before "symfony:composer:install",  "deployment:update_code:rewrite_params"
before "symfony:composer:install",  "deployment:update_code:rewrite_htaccess"
before "symfony:composer:install",  "deployment:frontend:rewrite_config"
before "symfony:composer:update",   "deployment:update_code:rewrite_params"
before "symfony:composer:update",   "deployment:update_code:rewrite_htaccess"
before "symfony:composer:update",   "deployment:frontend:rewrite_config"
before "symfony:cache:warmup",      "symfony:doctrine:migrations:migrate"

after "deployment:frontend:rewrite_config", "deployment:frontend:install"
after "deployment:frontend:install", "deployment:frontend:build"

after "deploy",             "deploy:cleanup"
after "deploy:cleanup",     "deployment:update_site_config"

# Logging
logger.level = Logger::MAX_LEVEL
