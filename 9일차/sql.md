```
SELECT b.con_datetime, b.con_title, u.user_name, de.dept_name, b.con_vc , du.duty_name 
            FROM ex_user_set u, ex_dept_set de, board_contents b, ex_duty_set du
            WHERE b.wr_user = u.user_id and b.wr_dept = de.dept_no and b.wr_duty = du.duty_no and b.con_no=3 ;
            
select u.user_name from ex_user_set u, board_contents b where u.user_id = b.re_user;
            
CREATE TABLE BOARD_CONTENTS 
(
  CON_NO NUMBER(5) NOT NULL 
, CON_DATETIME VARCHAR2(30) NOT NULL
, WR_USER NUMBER(10,0) NOT NULL
, WR_DEPT NUMBER(10,0) NOT NULL
, WR_DUTY NUMBER(10,0) NOT NULL
, CON_TITLE VARCHAR2(30) NOT NULL 
, CON_BODY VARCHAR2(100) NOT NULL 
, RE_USER NUMBER(10,0) NOT NULL 
, RE_DEPT NUMBER(10,0) NOT NULL 
, RE_DUTY NUMBER(10,0) NOT NULL
, CON_VC NUMBER(10)
,  CONSTRAINT pk_con PRIMARY KEY(con_no)
);

DROP TABLE BOARD_CONTENTS;

select * from board_contents;

DELETE FROM BOARD_CONTENTS;

INSERT INTO board_contents VALUES(1,to_char(sysdate,'yyyy.mm.dd hh24:mi:ss'),1,19,2572,'김설빈을 추천합니다.','김설빈사원에게 초콜릿을 받았습니다.',7,16,2575,5);
INSERT INTO board_contents VALUES(2,to_char(sysdate,'yyyy.mm.dd hh24:mi:ss'),2,20,2571,'박승찬을 추천합니다.','박승찬사원에게 초콜릿을 받았습니다.',8,17,2574,6);
INSERT INTO board_contents VALUES(3,to_char(sysdate,'yyyy.mm.dd hh24:mi:ss'),3,21,2573,'박시은을 추천합니다.','박시은사원에게 초콜릿을 받았습니다.',9,18,2572,7);
INSERT INTO board_contents VALUES(4,to_char(sysdate,'yyyy.mm.dd hh24:mi:ss'),4,19,2572,'김설빈을 추천합니다.','김설빈사원에게 초콜릿을 받았습니다.',7,16,2575,5);
INSERT INTO board_contents VALUES(5,to_char(sysdate,'yyyy.mm.dd hh24:mi:ss'),5,20,2571,'박승찬을 추천합니다.','박승찬사원에게 초콜릿을 받았습니다.',8,17,2574,6);
INSERT INTO board_contents VALUES(6,to_char(sysdate,'yyyy.mm.dd hh24:mi:ss'),6,21,2573,'박시은을 추천합니다.','박시은사원에게 초콜릿을 받았습니다.',9,18,2572,7);

rollback;


select ex_dept_set.dept_name,board_contents.con_vc FROM ex_dept_set join board_contents on ex_dept_set.dept_no = board_contents.wr_dept;

SELECT b.con_datetime, b.con_title, u.user_name, d.dept_name, b.con_vc FROM ex_user_set u, ex_dept_set d, board_contents b WHERE b.wr_user = u.user_id and b.wr_dept = d.dept_no;

CREATE OR REPLACE FORCE VIEW "V_EX_DEPT_SET" ("DEPT_NO", "DEPT_CD", "DEPT_NAME", "DEPT_ENG_NAME", "DEPT_NICK", "PARENT_NO", "LEVEL_NO", "LEADER_USER", "OFFICE_CD", "SORT_NO", "IS_USE", "LEVEL", "DEPT_NO_PATH", "DEPT_NAME_PATH", "DEPT_NAME_LVL", "IS_LEAF") AS 
  WITH DEPT AS
(
SELECT 
    D.*
    --, SYS_CONNECT_BY_PATH(D.DEPT_NAME, '/') AS DEPT_NAME_PATH
    , LEVEL
    , SYS_CONNECT_BY_PATH(LPAD(D.DEPT_NO, 4, '0'), '/') || '/' AS DEPT_NO_PATH
    , REPLACE(SYS_CONNECT_BY_PATH(D.DEPT_NAME, '/'), '/(주)하이테크엔지니어링/(주)하이테크엔지니어링', '/(주)하이테크엔지니어링/여수-본사') || '/' AS DEPT_NAME_PATH
    , lpad(' ', (level-1) * 4 ) || D.DEPT_NAME AS DEPT_NAME_LVL
    , DECODE(CONNECT_BY_ISLEAF, 1, 'Y', 'N') IS_LEAF
FROM EX_DEPT_SET D
START WITH PARENT_NO = 0
CONNECT BY PARENT_NO = PRIOR DEPT_NO
)
SELECT "DEPT_NO","DEPT_CD","DEPT_NAME","DEPT_ENG_NAME","DEPT_NICK","PARENT_NO","LEVEL_NO","LEADER_USER","OFFICE_CD","SORT_NO","IS_USE","LEVEL","DEPT_NO_PATH","DEPT_NAME_PATH","DEPT_NAME_LVL","IS_LEAF" FROM DEPT
WHERE 1 = 1 
    --AND DEPT_NO_PATH LIKE '%/10%'
;

SELECT b.con_datetime, b.con_title, u.user_name, de.dept_name, b.con_vc , du.duty_name, b.con_body, b.re_dept
                FROM ex_user_set u, ex_dept_set de, board_contents b, ex_duty_set du
                WHERE b.wr_user = u.uno and b.wr_dept = de.dept_no and b.wr_duty = du.duty_no;
```
