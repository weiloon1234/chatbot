import chalk from 'chalk';

console.log(
    chalk.bgGreen.black(' ✅  BUILD COMPLETED ') + ' ' +
    chalk.green('🚀 The build was successful!')
);

console.log(
    chalk.bgYellow.black(' ⚠️  IMPORTANT ') + ' ' +
    chalk.yellow('🛠️  Remember to run ') +
    chalk.bold.cyan('php artisan config:clear && php artisan cache:clear') +
    chalk.yellow(' to load latest assets!')
);
