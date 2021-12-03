CREATE TABLE board_member_se
(
    member_index number(10) NOT NULL,
    member_id varchar2(20) NOT NULL,
    member_password varchar2(20) NOT NULL,
    member_name varchar2(10) NOT NULL,
    member_age varchar2(3),
    member_phone number(12),
    member_gender varchar2(5),
    CONSTRAINT PK_board_memeber_se PRIMARY KEY(member_index)
);

CREATE SEQUENCE SEQ_board_memeber_se START WITH 1 INCREMENT BY 1 MAXVALUE 9999999999 CYCLE;    

INSERT INTO board_member_se(member_index, member_id, member_password,member_name, member_age,member_phone,member_gender) VALUES ( SEQ_BOARD_MEMEBER_SE.NEXTVAL , 'separk2111','separk2111','박시은','23','01090144658','woman');

