<?php

	require __DIR__ . '/baseController.php';
	
	/**
	 * 
	 */
	class controller extends BaseController
	{

		public function Home($request, $response, $args){
			return $this->renderer->render($response, 'index.phtml');
		}
		
		public function displayTodo($request, $response, $args){
			if($_SESSION['id'] == 0)
			{
				?>
					<a href='http://host1.local/authorization'>войдите в систему</a>
				<?php 
			}
			else{
				$userId = $_SESSION['id'];

				$result = $this->db->query('SELECT * FROM todo WHERE userId='.$userId);
				$rows = $result->fetchAll();
			
				return $this->renderer->render($response, 'todolist.phtml', [
					'todo_items' => $rows
				]);
			}
		}

		public function AddNewTask($request, $response, $args){
			$userId = $_SESSION['id'];

			$query = $this->db->prepare("INSERT INTO todo (`id`, `name`, `lastName`, `task`,`done`, `userId`) VALUES (NULL, :name, :last_name, :task, :done, :userId);"); 
			$query->bindParam(':name', $_POST['user_name']); 
			$query->bindParam(':last_name', $_POST['user_last_name']);
			$query->bindParam(':task', $_POST['task']);
			$doneCheck = ($_POST['done'] == 'on')? 1 : 0;
			$query->bindParam(':done', $doneCheck);
			$query->bindParam(':userId', $userId);
			$result = $query->execute();

			return $response->withStatus(200)->withHeader('Location', '/todolist');
		}

		public function Delete($request, $response, $args){
			$query = $this->db->prepare("DELETE FROM todo WHERE id=:id;"); 
			$query->bindParam(':id', $args['id']); 
			$result = $query->execute(); 

			if ($result) {
				return $response->withStatus(200)->withHeader('Location', '/todolist');
			}

			echo 'error';
		}

		public function EditForm($request, $response, $args){
			$result = $this->db->query('SELECT * FROM todo WHERE id='. (int)$args['id']); 
			$todo = $result->fetch();

			return $this->renderer->render($response, 'todo_edit.phtml', [
				'todo' => $todo
			]);
		}

		public function Edit($request, $response, $args){
			$query = $this->db->prepare("UPDATE todo SET name=:name, lastName=:lastName, task=:task, done=:done WHERE id=:id");
		 	$query->bindParam(':name', $_POST['user_name']);
		 	$query->bindParam(':lastName', $_POST['user_last_name']);
		 	$query->bindParam(':task', $_POST['user_task']);
		 	$doneCheck = ($_POST['user_done'] == 'on')? 1 : 0;
			$query->bindParam(':done', $doneCheck);
			$query->bindParam(':id', $args['id']);
		 	$result = $query->execute();

		 	if ($result) {
		 		return $response->withStatus(200)->withHeader('Location', '/todolist');
		 	}
		 	echo 'error';
		}

		public function Auth($request, $response, $args){
			return $this->renderer->render($response, 'authorization.phtml');
		}

		public function Login($request, $response, $args){
			$result = $this->db->query('SELECT * FROM authorization'); 
			$rows = $result->fetchAll();

			foreach ($rows as $row) {
				if($row['login'] == $_POST['login'] && 
					base64_decode($row['password']) == $_POST['password']){

					$login = $row['login'];
					$userId = $row['id'];

					$_SESSION['login'] = $login;
					$_SESSION['id'] = $userId;

					return $response->withStatus(200)->withHeader('Location', '/todolist');
				}
			}
			
			echo "Вы неправильно ввели логин и/или пароль";
		}

		public function LogOut($request, $response, $args){
			// Чистка массива $_SESSION
			session_unset();

		    return $response->withRedirect('/');
		}

		public function Register($request, $response, $args){
			return $this->renderer->render($response, 'register.phtml');
		}

		public function Registration($request, $response, $args){
			$login = $_POST['login'];

			$query = $this->db->prepare("INSERT INTO authorization (`id`, `login`, `password`) VALUES (NULL, :login, :password);");
			$query->bindParam(':login', $_POST['login']);
			$password = base64_encode($_POST['password']);
			$query->bindParam(':password', $password);
			$result = $query->execute();

			$userId = $this->db->lastInsertId();

			$_SESSION['login'] = $login;
			$_SESSION['id'] = $userId;

			return $response->withStatus(200)->withHeader('Location', '/todolist');
		}
	}