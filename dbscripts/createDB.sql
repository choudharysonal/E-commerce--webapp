create database CheapBook;
use CheapBook;

create table if not exists Author (
   ssn int(10) primary key,
   name varchar(32),
   address varchar(100),
   phone varchar(20)
);

create table if not exists Book (
   ISBN int(10) primary key,
   title varchar(32),
   year varchar(10),
   price varchar(20),
   publisher varchar(40)
);

create table if not exists WrittenBy (
    ssn int(10) references Author (ssn),
    ISBN int(10) references Book (ISBN),
    primary key(ssn,ISBN)
);

create table if not exists Warehouse (
    warehouseCode varchar(10) primary key,
    name varchar(32),
    address varchar(100),
    phone varchar(20)
);

create table if not exists Stocks (
    ISBN int(10) references Book (ISBN),
    warehouseCode varchar(10) references Warehouse (warehouseCode),
    number int(10),
    primary key(ISBN, warehouseCode)
);

create table if not exists Customers (
   username varchar(10) primary key,
   password varchar(32),
   address varchar(100),
   phone varchar(20),
   email varchar(45)
);

create table if not exists ShoppingBasket (
   basketID varchar(50) primary key,
   username	varchar(10) references Customers (username)
);

create table if not exists Contains (
    ISBN int(10) references Book (ISBN),
    basketID varchar(50) references ShoppingBasket (basketID),
    number int(10),
    primary key(ISBN, basketID)
);

create table if not exists ShippingOrder (
    ISBN int(10) references Book (ISBN),
    warehouseCode varchar(10) references Warehouse (warehouseCode),
    username varchar(10) references Customers (username),
    number int(10),
    primary key(ISBN,warehouseCode,username)
);