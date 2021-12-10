CREATE TABLE BOARD_CONTENTS 
(
  CON_NO NUMBER(5) NOT NULL 
, CON_DATETIME VARCHAR2(30) NOT NULL
, WR_USER VARCHAR2(64) NOT NULL
, WR_DEPT NUMBER(10,0) NOT NULL
, WR_DUTY NUMBER(10,0) NOT NULL
, CON_TITLE VARCHAR2(30) NOT NULL 
, CON_BODY VARCHAR2(100) NOT NULL 
, RE_USER VARCHAR2(64) NOT NULL 
, RE_DEPT NUMBER(10,0) NOT NULL 
, RE_DUTY NUMBER(10,0) NOT NULL
, CON_VC NUMBER(10)
,  CONSTRAINT pk_con PRIMARY KEY(con_no)
);

DROP TABLE BOARD_CONTENTS;

ALTER TABLE EX_USER_SET ADD CONSTRAINT UK_USER_ID UNIQUE(USER_ID);

select * from board_contents;

DELETE FROM BOARD_CONTENTS;

INSERT INTO board_contents VALUES(1,to_char(sysdate,'yyyy.mm.dd hh24:mi:ss'),'test','90','2575','김설빈을 추천합니다.','김설빈사원에게 초콜릿을 받았습니다.','test2','90','2575','');
INSERT INTO board_contents VALUES(2,to_char(sysdate,'yyyy.mm.dd hh24:mi:ss'),'test','90','2575','박승찬을 추천합니다.','박승찬사원에게 초콜릿을 받았습니다.','test2','90','2575','');
INSERT INTO board_contents VALUES(3,to_char(sysdate,'yyyy.mm.dd hh24:mi:ss'),'test','90','2575','박시은을 추천합니다.','박시은사원에게 초콜릿을 받았습니다.','test2','90','2575','');

rollback;
