/* bookstrageデータベース作成 */
CREATE DATABASE bookstrage;

USE bookstrage;
/* booksテーブル作成 */
CREATE TABLE books (
  isbn VARCHAR(17) NOT NULL PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  price INT NOT NULL,
  page INT NOT NULL,
  date DATE NOT NULL
) DEFAULT CHARACTER SET=utf8;
