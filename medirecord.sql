create database Medirecord;
use Medirecord;

create table Paciente(
Rut varchar  (10) primary key ,
Nombre varchar (50),
Telefono varchar(11) ,
Corre_electronico varchar (50)
);

create table Funcionario(
ID int auto_increment primary key, 
Rut varchar (10),
nombre varchar (30) , 
Pass varchar (30) , 
rol varchar (20)
);
create table Historial_Mensajes(
ID int auto_increment primary key, 
ID_funcionario int,
Hora_carga datetime,
Fecha_envio  datetime,
foreign key (ID_funcionario) references Funcionario(ID)
);

create table Comentarios (
id int auto_increment primary key,
Rut varchar (10),
nombre varchar(30),
mensaje text ,
fecha timestamp default current_timestamp,
revisado boolean default false

);

create table Hora(
ID int auto_increment primary key,
Rut_Paciente varchar(10),
Profesional varchar(50) ,
Tipo_Atencion varchar(50),
Especialidad varchar (50),
Dia varchar (10),
Hora_Agandada varchar(6),
Asistencia varchar (20),
Fecha_envio datetime,
ID_Envio int,
foreign key (Rut_Paciente) references Paciente(rut),
foreign key (ID_Envio) references Historial_Mensajes(ID)
);

-- drop database Medirecord;
select * from paciente;
select * from hora;
select * from Funcionario;
select * from Historial_Mensajes;
select * from Comentarios;
use Medirecord;
-- update hora set Asistencia = "Por confirmar" where ID =2;

insert into Funcionario (nombre , pass,rol) values ('admin', 'adminpass','admin');
show tables