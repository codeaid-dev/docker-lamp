/* quizデータベース作成 */
CREATE DATABASE quiz;

USE quiz;
/* questionsテーブル作成 */
CREATE TABLE questions (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  question VARCHAR(255) NOT NULL,
  answer VARCHAR(255) NOT NULL
) DEFAULT CHARACTER SET=utf8;
