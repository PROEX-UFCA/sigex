{ pkgs ? import <nixpkgs> {} }:

let
  php = pkgs.php83.withExtensions ({ enabled, all }: enabled ++ [
    all.pdo_mysql
  ]);
in
pkgs.mkShell {
  buildInputs = [
    php
    pkgs.php83Packages.composer
    pkgs.nodejs
  ];

  shellHook = ''
    export PATH="$PWD/vendor/bin:$PATH"
    echo "Ambiente Laravel pronto. PHP $(php -r 'echo PHP_VERSION;') + Composer."
  '';
}
