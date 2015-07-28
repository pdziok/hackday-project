#!/bin/rake

#Needed for task desc rake/gem '= 0.9.2.2'
if defined? Rake::TaskManager.record_task_metadata
  Rake::TaskManager.record_task_metadata = true
end

rootPath = Rake.application.original_dir
buildPath = 'data/build'
logPath = 'data/log'
cachePath = 'data/cache'
srcPath = 'src'
codeStyle = 'PSR2'
testPath = "test"
coveragePath = 'coverage'
testTypes = %w(unit)
defaultTestType = "unit"

buildDirs = %w(api code-browser coverage/html coverage/clover logs pdepend)

desc "Cleanup build artifacts"
task :clean do |task|
    puts task.comment
    FileUtils.rm_rf buildPath
    FileUtils.rm_rf "#{cachePath}/**"
end

desc "Prepare for build"
task :prepare do |task|
    puts task.comment
    buildDirs.each do |dirName|
        FileUtils.mkdir_p("#{buildPath}/#{dirName}")
    end
end

desc "Prepare directories for deploy"
task :prepareDeploy do |task|
    puts task.comment
    unless File.directory? cachePath
        FileUtils.mkdir_p cachePath
    end
    FileUtils.chmod 0777, cachePath
    unless File.directory? logPath
        FileUtils.mkdir_p logPath
    end
    FileUtils.chmod 0777, logPath
    unless File.directory? buildPath
        FileUtils.mkdir_p buildPath
    end
    FileUtils.chmod 0777, buildPath
end

desc "Perform syntax check of sourcecode files"
task :lint do |task|
    puts task.comment
    system_check "find #{srcPath} -name '*.php' -exec php -l {} \\; | (! grep -v 'No syntax error')"
end

desc "Measure project size using PHPLOC"
task :phploc do |task|
    puts task.comment
    system_check "vendor/bin/phploc --log-csv #{buildPath}/logs/phploc.csv #{srcPath}"
end

desc "Calculate software metrics using PHP_Depend"
task :pdepend do |task|
    puts task.comment
    system_check "vendor/bin/pdepend --jdepend-xml=#{buildPath}/logs/jdepend.xml" +
             " --jdepend-chart=#{buildPath}/pdepend/dependencies.svg" +
             " --overview-pyramid=#{buildPath}/pdepend/overview-pyramid.svg" +
             " #{srcPath}"
end

desc "Perform project mess detection using PHPMD and print human readable output. Intended for usage on the command line before committing."
task :phpmd do |task|
    puts task.comment
    system "vendor/bin/phpmd #{srcPath} text scripts/php/phpmd.xml"
end

desc "Perform project mess detection using PHPMD creating a log file for the continuous integration server"
task :phpmdCi do |task|
    puts task.comment
    system "vendor/bin/phpmd #{srcPath} xml scripts/php/phpmd.xml --reportfile #{buildPath}/pmd.xml"
end

desc "Find coding standard violations using PHP_CodeSniffer and print human readable output. Intended for usage on the command line before committing."
task :phpcs do |task|
    puts task.comment
    system_check "vendor/bin/phpcs --standard=#{codeStyle} #{srcPath}"
end

desc "Find coding standard violations using PHP_CodeSniffer creating a log file for the continuous integration server"
task :phpcsCi do |task|
    puts task.comment
    system_check "vendor/bin/phpcs" +
             " --report=checkstyle" +
             " --report-file=#{buildPath}/logs/checkstyle.xml" +
             " --standard=#{codeStyle} #{srcPath}" +
             " > /dev/null"
end

desc "Find duplicate code using PHPCPD"
task :phpcpd do |task|
    puts task.comment
    system_check "vendor/bin/phpcpd --log-pmd #{buildPath}/logs/pmd-cpd.xml #{srcPath} > /dev/null"
end

desc "Generate API documentation using phpDoc"
task :phpdoc do |task|
    puts task.comment
    system_check "vendor/bin/phpdoc -t #{buildPath}/api -d #{srcPath}"
end

namespace :composer do

    desc "Update composer's dependencies"
    task :update, [:params] do |task, args|
        puts task.comment
        unless File.exist?('composer.phar')
            system_check "curl -s http://getcomposer.org/installer | php"
        end

        system_check "php -d \"apc.enable_cli=off\" composer.phar self-update"
        system_check "php -d \"apc.enable_cli=off\" composer.phar update #{args.params} --prefer-dist"
    end

    desc "Update composer's dependencies for development"
    task :dev do |tas|
        puts task.comment
        Rake::Task["composer:update"].invoke()
    end

    desc "Update composer's dependencies for production"
    task :prod do |tas|
        puts task.comment
        Rake::Task["composer:update"].invoke('--no-dev')
    end
end

desc "Install composer's dependencies"
task :composer do |task|
    puts task.comment
    Rake::Task['composer:prod'].invoke
end

desc "Run tests on given type (unit|integration)"
task :test do |task|
    puts task.comment

    cloverPath = "#{buildPath}/#{coveragePath}/clover"
    coverageFullPath = "#{buildPath}/#{coveragePath}"

    testTypes.each do |testType|

        currentModulePath = testPath
        if (!File.exist?("./#{currentModulePath}/#{testType}/phpunit.xml"))
            system_check "cp #{currentModulePath}/#{testType}/phpunit.xml.dist #{currentModulePath}/#{testType}/phpunit.xml"
        end

        coverageCovFile = "#{buildPath}/#{coveragePath}/#{testType}.cov"

        system_check "php vendor/bin/phpunit -c " +
                " $PWD/#{currentModulePath}/#{testType}/phpunit.xml" +
                " --log-junit $PWD/#{buildPath}/logs/test-#{testType}.xml" +
                " --coverage-clover $PWD/#{buildPath}/logs/clover-#{testType}.xml" +
                " --coverage-html $PWD/#{buildPath}/#{coveragePath}/#{testType}" +
                " --coverage-php $PWD/#{coverageCovFile}" +
                " --coverage-text" +
                " $PWD/#{currentModulePath}/#{testType}/"
    end

    system_check <<END
    php -d error_reporting=0 vendor/bin/phpcov merge --html="#{coverageFullPath}/tg-api" #{coverageFullPath}
    php -d error_reporting=0 vendor/bin/phpcov merge --clover="#{buildPath}/logs/tg-api.xml" #{coverageFullPath}
END

end

desc "Aggregate tool output with PHP_CodeBrowser"
task :phpcb do |task|
      puts task.comment
      system_check "vendor/bin/phpcb" +
            " --log #{buildPath}/logs/" +
            " --source #{srcPath}" +
            " --output #{buildPath}/code-browser"
end

desc "Make copy of environment-specific dist configuration file into config.php (testing production)"
task :setEnv, [:newEnv] do |task, args|
    puts task.comment
    if (File.exist?("config/config.#{args.newEnv}.php-dist"))
        system_check "cp config/config.#{args.newEnv}.php-dist config/config.php"
    end
end

desc "Install npm dependencies"
task :npm do |task|
    system_check "npm install"
end

desc "Install npm dependencies"
task :apidoc do |task|
    system_check "$PWD/node_modules/.bin/apidoc -i src/ -o web/apidoc"
end

module Rake
    class Application
        attr_accessor :current_task
    end
    class Task
        alias :old_execute :execute
        def execute(args=nil)
              Rake.application.current_task = @name
              old_execute(args)
        end
    end
end

module Kernel
    def system_check(args=nil)
        system(args)
        unless $?.success?
            puts "Task #{Rake.application.current_task} failed"
            exit!(1)
        end
    end
end

testType = ENV["testType"] || defaultTestType

task :build => ["lint","phploc","pdepend","phpmdCi","phpcs", "phpcsCi","phpcpd","phpdoc","phpcb","apidoc"] do
    Rake::Task["test"].invoke
end

task :install => ["prepare","prepareDeploy","composer:dev","npm"]

task :default => ["install"]
