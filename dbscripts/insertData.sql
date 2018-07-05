insert into Author values(0001234567,"Greg Gagne","600 W Mitchell Street","6828939552");
insert into Author values(0005671234,"William Stallings","145 South Cooper Street","6828939772");
insert into Author values(0001256734,"Abraham Silberschatz","UTA Boulevard Road","6828939445");
insert into Author values(0001267345,"Peter Baer Galvin","1000 Benge Road, Arlington","6828939266");

insert into Book values(1126509627,"Operating Systems Principles","2006","450.00","Wiley India Pvt. Limited");
insert into Book values(0226276509,"Distributed Programming","2009","200.00","O'Reily");
insert into Book values(0081509627,"Computer Organization","1999","320.00","Prentice Hall");
insert into Book values(0150819645,"Cloud Computing","2010","550.00","Key Stone Limited");
insert into Book values(1850800648,"Oops Concepts","2003","346.00","ABC Private Limited");

insert into WrittenBy values(0001234567,1126509627);
insert into WrittenBy values(0001234567,0226276509);
insert into WrittenBy values(0005671234,0081509627);
insert into WrittenBy values(0001256734,0150819645);
insert into WrittenBy values(0001267345,1850800648);

insert into Warehouse values("w100","ArlingtonWarehouse","1000 West Mitchell Street","6827039443");
insert into Warehouse values("w101","AustinWarehouse","926 North Cooper Street, Austin","6827439122");

insert into Stocks values(1126509627,"w100",10);
insert into Stocks values(0081509627,"w100",4);
insert into Stocks values(0226276509,"w100",3);
insert into Stocks values(0150819645,"w100",15);
insert into Stocks values(1850800648,"w100",1);
insert into Stocks values(1126509627,"w101",2);
insert into Stocks values(0081509627,"w101",1);
insert into Stocks values(0226276509,"w101",1);
insert into Stocks values(0150819645,"w101",6);
insert into Stocks values(1850800648,"w101",10);



