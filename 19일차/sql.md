```
select re_user, sum(con_vc) as con_vc, sum(con_good) as con_good from board_contents group by re_user;
select re_user, sum(con_vc) + (sum(con_good))*2 + (sum(con_comment))*3 + count(*) as score from board_contents group by re_user;
select re_user, sum(con_vc) , (sum(con_good))*2 , (sum(con_comment))*3 , (count(*))*5 as score from board_contents group by re_user;

select re_user, count(*) as score from board_contents group by re_user;

select count(*) as con_comment from board_comment group by con_no;
delete from board_comment;

update board_contents set con_comment = 0;

SELECT * FROM V_SCORE ORDER BY SCORE DESC;

SELECT * FROM (SELECT * FROM V_SCORE ORDER BY SCORE DESC) WHERE ROWNUM=1;

SELECT ROWNUM AS RANK ,RE_USER, SCORE FROM (SELECT * FROM V_SCORE ORDER BY SCORE DESC) WHERE ROWNUM <= 5;



select rownum as num, re_user, score from V_SCORE ORDER BY SCORE DESC;

SELECT u.user_name, de.dept_name, du.duty_name, v.re_user, b.con_no
FROM ex_user_set u, ex_dept_set de, ex_duty_set du, v_score v, board_contents b
WHERE u.uno = v.re_user and u.dept_id = de.dept_no and u.duty_id = du.duty_no;
```
