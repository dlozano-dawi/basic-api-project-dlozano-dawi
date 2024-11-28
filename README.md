### **Contexto del Proyecto**

Se ha diseñado una API básica con autentificación en PHP, esta consiste en una API para bancos en la cual se podrán crear tokens de usuario para el uso de esta, usuarios del banco los cuales podrán tener varias cuentas bancarias, los usuarios tedrán un DNI/NIE como identificador único. 

### **Requisitos funcionales**

1. **Definición de los Endpoints de la API**  
   * Se han definido los endpoints necesarios para interactuar con los recursos del banco. Esto incluye operaciones CRUD para usuarios, tokens y cuentas. 
   * Endpoints:
      * Crear usuarios (POST)
      * Ver todos los usuarios (GET)
      * Crear cuentas bancarias a usuarios (un usuario puede tener varias cuentas) (POST)
      * Ver todas las cuentas (GET)
      * Operar con dinero (extraer e ingresar) (PUT)
      * Elimar x cuenta (DELETE)

2. **Base de Datos**  
   * Diseñar el esquema relacional que soportará la API. Incluir las tablas esenciales para la gestión de Tokens, usuarios y cuentas.  
   * Definir las relaciones entre las tablas, como la asociación de varias cuentas a un usuario.

  **Crear DB:**
  ```
    CREATE DATABASE bank;
  ```
  ```
    USE bank;
  ```
  ```
  CREATE TABLE `bank`.`user` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(45) NULL,
    `lastname` VARCHAR(45) NULL,
    `dni` VARCHAR(9) NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `dni_UNIQUE` (`dni` ASC) VISIBLE);
  ```
  ```
  CREATE TABLE `bank`.`account` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `money` DECIMAL(15) NULL DEFAULT 0,
    `user_id` INT NULL,
    PRIMARY KEY (`id`),
    INDEX `id_idx` (`user_id` ASC) VISIBLE,
    CONSTRAINT `user_id`
      FOREIGN KEY (`user_id`)
      REFERENCES `bank`.`user` (`id`)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION);
  ```
  ```
  CREATE TABLE `bank`.`tokens` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `user` VARCHAR(45) NULL,
    `token` VARCHAR(45) NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `user_UNIQUE` (`user` ASC) VISIBLE,
    UNIQUE INDEX `token_UNIQUE` (`token` ASC) VISIBLE);
  ```

4. **Seguridad**  
   * Se ha implementado un sistema de seguridad basado en ApiKeys, para obtener una tienes que hacer una llamada Post a URL.../API/tokenController.php
     * Con el Body:
        ```
        {
          "user": "David"
        }
        ```
     * Respuesta:
        ```
        {
          "mensaje": "Se ha creado un token para este usuario: {token}"
        }
        ```
   * Obtener el token con una llamada GET a /API/tokenController.php?user=David
     * Respuesta:
        ```
        {
          "token": "{token}"
        }
        ```
   * Por ultimo tendremos que añadir el token al header de las peticiones como un bearer token


5. ### **Referencia de Endpoints.** 

   * **Endpoints de usuarios** 
     * Crear un usuario haciendo una llamada POST a URL.../API/userController.php
       * Estructura del body:
         ```
           {
             "name": "David",
             "lastname": "Lozano",
             "dni": "45186232k"
           }
         ```
       * Respuesta:
         ```
         {
           "mensaje": "Cuenta creada"
         }
         ```
     * Obtener todos los usuarios haciendo una petición GET URL.../API/userController.php
       * Respuesta:
           ```
           {
             "usuarios": [
               {
                 "id": 1,
                 "name": "David",
                 "user_id": "Lozano"
                 "dni": "45276543L"
               }
             ]
           }
           ``` 
   * **Endpoints de cuentas** 
     * Crear una cuenta para un usuario haciendo una petición POST URL.../API/accountController.php
       * Estructura del body:
         * Money será la cantidad base que tenga la cuenta
         * user_id será el id del usuario al que se le asignará la cuenta 
         ```
           {
             "money": "50000",
             "user_id": "1"
           }
         ```
         * Respuesta:
         ```
         {
           "mensaje": "Cuenta bancaria creada"
         }
         ```
     * Obtener todos las cuentas haciendo una petición GET URL.../API/accountController.php
       * Respuesta:
           ```
           {
             "cuentas": [
               {
                 "id": 1,
                 "money": 10000,
                 "user_id": 1
               },
               {
                 "id": 2,
                 "money": 20000,
                 "user_id": 1
               }
             ]
           }
           ```
     * Obtener todos los usuarios haciendo una petición PUT URL.../API/accountController.php
       * Indicaremos la cantidad a ingreasar o extraer (si introducimos un - se extraerá)
       * Ejemplo de sacar dinero:
         ```
         {
           "id": "3",
           "money": "-10000"
         }
         ```
       * Ejemplo de ingreso:
         ```
         {
           "id": "3",
           "money": "10000"
         }
         ```
       * Respuesta:
         ```
         {
           "mensaje": "Operacion realizada correctamente"
         }
         ```
     * Eliminar una cuenta haciendo una petición DELETE URL.../API/accountController.php?id=3 en la cual indicaremos la id de la cuenta a eliminar
       * Respuesta:
         ```
         {
           "mensaje": "Cuenta eliminada"
         }
         ```
