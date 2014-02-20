<?php
/*##################################################
 *                       SandboxGraphicsCSSController.class.php
 *                            -------------------
 *   begin                : May 05, 2012
 *   copyright            : (C) 2012 Kevin MASSY
 *   email                : kevin.massy@phpboost.com
 *
 *
 ###################################################
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

class SandboxGraphicsCSSController extends ModuleController
{
	private $view;
	private $lang;
	
	public function execute(HTTPRequestCustom $request)
	{
		$this->init();
		
		$this->build_view();
		
		return $this->generate_response();
	}
	
	private function init()
	{
		$this->lang = LangLoader::get('common', 'sandbox');
		$this->view = new FileTemplate('sandbox/SandboxGraphicsCSSController.tpl');
		$this->view->add_lang($this->lang);
	}
	
	private function build_view()
	{
		$messages = array(
			MessageHelper::display($this->lang['css.message_success'], MessageHelper::SUCCESS),
			MessageHelper::display($this->lang['css.message_notice'], MessageHelper::NOTICE),
			MessageHelper::display($this->lang['css.message_warning'], MessageHelper::WARNING),
			MessageHelper::display($this->lang['css.message_error'], MessageHelper::ERROR),
			MessageHelper::display($this->lang['css.message_question'], MessageHelper::QUESTION)
		);
		
		foreach ($messages as $message)
		{
			$this->view->assign_block_vars('messages', array('VIEW' => $message));
		}
		
		$pagination = new ModulePagination(2, 15, 5);
		$pagination->set_url(new Url('#%d'));
		$this->view->put('PAGINATION', $pagination->display());
	}
	
	private function generate_response()
	{
		$response = new SiteDisplayResponse($this->view);
		$graphical_environment = $response->get_graphical_environment();
		$graphical_environment->set_page_title($this->lang['module_title'] . ' - ' . $this->lang['title.css']);
		
		$breadcrumb = $graphical_environment->get_breadcrumb();
		$breadcrumb->add($this->lang['module_title'], SandboxUrlBuilder::home()->rel());
		$breadcrumb->add($this->lang['title.css'], SandboxUrlBuilder::css()->rel());
		
		return $response;
	}
}
?>